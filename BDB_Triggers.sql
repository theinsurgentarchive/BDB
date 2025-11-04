DELIMITER //

DROP TRIGGER IF EXISTS toggle_users_active//
DROP TRIGGER IF EXISTS delete_comments//
DROP TRIGGER IF EXISTS set_comment_depth//
DROP TRIGGER IF EXISTS prevent_direct_book_insert//
DROP TRIGGER IF EXISTS prevent_direct_bookgenre_insert//
DROP TRIGGER IF EXISTS prevent_direct_form_insert//
DROP TRIGGER IF EXISTS prevent_direct_formgenre_insert//

CREATE TRIGGER set_comment_depth
BEFORE INSERT ON comments FOR EACH ROW BEGIN
    DECLARE D INT;
    IF NEW.parent_id IS NULL THEN
        SET NEW.depth = 0;
    ELSE
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

CREATE TRIGGER toggle_users_active
BEFORE UPDATE ON users FOR EACH ROW BEGIN
    IF OLD.is_active = 1 AND NEW.is_active = 0 THEN
        SET NEW.deletion_date = CURRENT_TIMESTAMP;
    ELSEIF OLD.is_active = 0 AND NEW.is_active = 1 THEN
        SET NEW.deletion_date = NULL;
    END IF;
END//

CREATE TRIGGER delete_comments
BEFORE DELETE ON comments FOR EACH ROW BEGIN
    DECLARE has_child INT DEFAULT 0;
    IF @skip_trig IS NULL THEN
        SELECT EXISTS (
            SELECT 1 FROM comments C1 WHERE C.parent_id = OLD.comment_id 
        ) INTO has_child;

        IF has_child THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
            'Cannot hard-delete a comment that has replies, use deleteComment()';
        END IF;
    END IF;
END//

CREATE TRIGGER prevent_direct_book_insert
BEFORE INSERT ON books FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into books, use addBook() or formToBook()';
    END IF;
END//

CREATE TRIGGER prevent_direct_bookgenre_insert
BEFORE INSERT ON bookgenres FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into booksgenres, use addBook() or formToBook()';
    END IF;
END//

CREATE TRIGGER prevent_direct_form_insert
BEFORE INSERT ON forms FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into forms, use addForm()';
    END IF;
END//

CREATE TRIGGER prevent_direct_formgenre_insert
BEFORE INSERT ON formgenres FOR EACH ROW BEGIN
    IF @allow IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Cannot directly insert into formgenres, use addForm()';
    END IF;
END//

DELIMITER ;