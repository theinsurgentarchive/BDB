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
    SELECT EXISTS (
        SELECT 1 FROM comments C1 WHERE C.parent_id = OLD.comment_id 
    ) INTO has_child;

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid FOR UPDATE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Comment not found';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid AND deleted_by IS NULL
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Comment already deleted';
    END IF;

    SET A = has_child;
    IF (A > 1 OR A < 0) OR A IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid action state';
    END IF;
    
    SELECT C.user_id, C.parent_id, C.book_id,
    C.comment_text, C.depth, C.creation_date INTO
    C_uid, C_pid, C_bid, C_text, C_depth, C_creation_date FROM comments C
    WHERE C.comment_id = p_comment_id;

    SET @parent_id_hash = UNHEX(SHA2(C_pid), 256);
    SET @text_hash = UNHEX(SHA2(C_text), 256);
    SET @row_hash = UNHEX(
        SHA2(CONCAT_WS(
            '::', @parent_id_hash, ',', @text_hash
        ), 256)
    );

    INSERT INTO shadowcomments (
        book_id, comment_id, creation_date, user_id, parent_id,
        comment_text, depth, row_hash, deleted_by, reason, action
    ) VALUES(
      C_bid, cid, C_creation_date, C_uid, @parent_id_hash,
      @text_hash, C_depth, @row_hash, uid, reason, A
    );

    INSERT INTO shadowidmaps (C_uid, hashs) VALUES (C_uid, JSON_OBJECT(
        'parent_id', @parent_id_hash, 'comment_text', @text_hash)
    );

    IF has_child THEN
        UPDATE comments SET user_id = NULL, comment = '[DELETED COMMENT]'
        WHERE comment_id = cid;
    ELSE
        SET @skip_trig = 1;
        DELETE FROM comments WHERE comment_id = cid;
        SET @skip_trig = NULL;
    END IF;
END//

CREATE PROCEDURE restoreComment(IN cid INT, IN uid INT) BEGIN
END//
DELIMITER ;