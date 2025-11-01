DELIMITER //
DROP TRIGGER IF EXISTS set_form_dupes//
DROP TRIGGER IF EXISTS toggle_users_active//
DROP TRIGGER IF EXISTS delete_comments//
DROP TRIGGER IF EXISTS set_comment_depth//

--Answers Question:
--"How does a user differentiate between forms where they have the same user_id and isbn when searching?"
CREATE TRIGGER set_form_dupes
BEFORE INSERT ON forms FOR EACH ROW BEGIN
    DECLARE next INT;
    DECLARE lock VARCHAR(64);
    DECLARE ok INT;
    IF NEW.dupe_num IS NULL THEN
        SET lock = CONCAT('forms:', SUBSTRING(
            SHA2(CONCAT(NEW.user_id, ':', NEW.isbn), 256), 1, 48)
        );

        SELECT GET_LOCK(lock, 10) INTO ok;
        IF ok <> 1 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'dupe_num lock timeout';
        END IF;

        SELECT COALESCE(MAX(F.dupe_num) + 1, 0) INTO next FROM forms F 
        WHERE F.user_id = NEW.user_id AND F.isbn = NEW.isbn;
        SET NEW.dupe_num = next;

        SELECT RELEASE_LOCK(lock) INTO ok;
    END IF;
END//

--Answers Question:
--"How does a user know what comments are replies to others?"
CREATE TRIGGER set_comment_depth
BEFORE INSERT ON comments FOR EACH ROW BEGIN
    IF NEW.parent_id IS NULL THEN
        SET NEW.depth = 0;
    ELSE
        DECLARE D INT;
        WITH RECURSIVE DS (pid, d) AS (
            SELECT NEW.parent_id, 1 UNION ALL SELECT C.parent_id, DS.d + 1 FROM
            comments C JOIN DS ON C.comment_id = DS.pid
        ) SELECT MAX(d) INTO D FROM DS;
        
        IF D IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid parent chain';
        END IF;

        SET NEW.depth = D;
    END IF;
END//

--Answers Question:
--"When a users goes to de/re-activate their account, how should the row change?"
CREATE TRIGGER toggle_users_active
BEFORE UPDATE ON users FOR EACH ROW BEGIN
    IF OLD.is_active = 1 AND NEW.is_active = 0 THEN
        SET NEW.deletion_date = CURRENT_TIMESTAMP;
    ELSEIF OLD.is_active = 0 AND NEW.is_active = 1 THEN
        SET NEW.deletion_date = NULL;
    END IF;
END//

--Answers Question:
--"When a user removes a comment, either with children or without, what happens to the child comments?"
CREATE TRIGGER delete_comments
BEFORE DELETE ON comments FOR EACH ROW BEGIN
    DECLARE has_child INT DEFAULT 0;
    IF @skip_trig IS NULL THEN
        SELECT EXISTS (
            SELECT 1 FROM comments C1 WHERE C.parent_id = OLD.comment_id 
        ) INTO has_child;

        IF has_child THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
            'Cannot hard-delete a comment that has replies, use deleteComment(INT comment_id, INT user_id, ENUM reason)';
        END IF;
    END IF;
END//

DELIMITER ;