DELIMITER //
DROP PROCEDURE IF EXISTS deleteComment//
DROP PROCEDURE IF EXISTS restoreComment//
CREATE PROCEDURE deleteComment(
    IN cid INT, IN uid INT, IN reason ENUM('USER', 'ADMIN', 'NONE')
) BEGIN
    DECLARE C_uid INT;
    DECLARE C_pid INT;
    DECLARE C_bid INT;
    DECLARE C_text TEXT;
    DECLARE C_depth INT;
    DECLARE C_creation_date TIMESTAMP;
    DECLARE has_child INT DEFAULT 0;
    DECLARE A INT;

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid FOR UPDATE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Comment not found';
    END IF;

    SELECT EXISTS (
        SELECT 1 FROM comments WHERE parent_id = cid 
    ) INTO has_child;
    SET A = has_child;

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid AND deleted_by IS NULL
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Comment already deleted';
    END IF;

    IF (A > 1 OR A < 0) OR A IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid action state';
    END IF;
    
    SELECT C.user_id, C.parent_id, C.book_id,
    C.comment_text, C.depth, C.creation_date INTO
    C_uid, C_pid, C_bid, C_text, C_depth, C_creation_date FROM comments C
    WHERE C.comment_id = cid;

    INSERT INTO shadowcomments (
        book_id, comment_id, creation_date, user_id, parent_id,
        comment_text, depth, deleted_by, reason, action
    ) VALUES(
      C_bid, cid, C_creation_date, C_uid, C_pid,
      C_text, C_depth, uid, reason, A
    );

    IF has_child THEN
        UPDATE comments SET user_id = NULL, comment_text = '[DELETED COMMENT]',
        WHERE comment_id = cid;
    ELSE
        SET @skip_trig = 1;
        DELETE FROM comments WHERE comment_id = cid;
        SET @skip_trig = NULL;
    END IF;
END//

CREATE PROCEDURE restoreComment(IN cid INT) BEGIN
    DECLARE C_exists INT DEFAULT 0;
    SELECT 1 INTO C_exists FROM comments WHERE comment_id = cid;
    IF C_exists THEN
        DECLARE uid INT;
        DECLARE C_text TEXT;

        SELECT user_id, comment_text INTO uid, text_hash
        FROM shadowcomments WHERE comment_id = cid;

        UPDATE comments SET user_id = uid, comment_text = C_text
        WHERE comment_id = cid;
    ELSE
        DECLARE bid INT;
        DECLARE pid INT;
        DECLARE uid INT;
        DECLARE C_text TEXT;
        DECLARE C_creation_date TIMESTAMP;
        DECLARE C_depth INT;

        SELECT book_id, parent_id, user_id, comment_text, depth, creation_date
        INTO bid, pid, uid, C_text, C_depth, C_creation_date FROM shadowcomments
        WHERE comment_id = cid;

        INSERT INTO comments(
            book_id, comment_id, user_id, parent_id, creation_date,
            comment_text, depth, deletion_date
        ) VALUES (bid, cid, uid, pid, C_creation_date, C_text, C_depth, NULL);
        DELETE FROM shadowcomments WHERE comment_id = cid;
    END IF;
END//
DELIMITER ;