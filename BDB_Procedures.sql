DELIMITER //
DROP PROCEDURE IF EXISTS delete_comment

CREATE PROCEDURE delete_comment(IN cid INT, IN uid INT, IN R INT) BEGIN
    DECLARE LB LONGBLOB;
    DECLARE LT LONGTEXT;
    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid FOR UPDATE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Comment not found';
    END IF;

    SELECT JSON_OBJECT(
    ) INTO LT FROM comments C WHERE C.comment_id = cid;
    SET LB = TO_BASE64(COMPRESS(LT));
    
    INSERT INTO shadowcomments (
        book_id, comment_id, creation_date, comment_data, deleted_by, reason
    ) SELECT C.book_id, C.comment_id, C.creation_date, LB, uid, R, A FROM
    comment C WHERE C.comment_id = cid ON DUPLICATE KEY UPDATE 
    deleted_by = VALUES(deleted_by),
    comment_data = VALUES(comment_data),
    deletion_date = CURRENT_TIMESTAMP;
    

END//

--ChatGPT Procedure, modify and understand first
/*
CREATE PROCEDURE sp_user_delete_comment_auto(
  IN p_comment_id INT,
  IN p_deleted_by INT
)
BEGIN
  DECLARE v_book_id INT;
  DECLARE v_payload LONGBLOB;
  DECLARE v_has_child BOOLEAN DEFAULT FALSE;

  -- 1) Lock the target row so it can't change while we work
  SELECT book_id
    INTO v_book_id
  FROM comments
  WHERE comment_id = p_comment_id
  FOR UPDATE;

  IF v_book_id IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Comment not found';
  END IF;

  -- 2) Build snapshot as JSON, then obfuscate: BASE64(COMPRESS(JSON))
  SELECT TO_BASE64(COMPRESS(JSON_OBJECT(
           'comment_id',    c.comment_id,
           'book_id',       c.book_id,
           'user_id',       c.user_id,
           'parent_id',     c.parent_id,
           'creation_date', DATE_FORMAT(c.creation_date, '%Y-%m-%d %H:%i:%s'),
           'comment',       c.comment,
           'depth',         c.depth,
           'deletion_date', IFNULL(DATE_FORMAT(c.deletion_date, '%Y-%m-%d %H:%i:%s'), NULL),
           'is_active',     c.is_active
         )))
    INTO v_payload
  FROM comments c
  WHERE c.comment_id = p_comment_id;

  -- 3) Lock the "children" key-range to avoid a race (prevents new replies)
  -- Requires InnoDB + REPEATABLE READ (default) and an index on parent_id.
  SELECT EXISTS(
           SELECT 1
           FROM comments
           WHERE parent_id = p_comment_id
           FOR UPDATE
         )
    INTO v_has_child;

  -- 4) Archive once per comment_id (idempotent)
  INSERT INTO deleted_comments (comment_id, book_id, deleted_by, action, codec, payload)
  VALUES (p_comment_id, v_book_id, p_deleted_by, IF(v_has_child,'SOFT','HARD'), 'JSONZ', v_payload)
  ON DUPLICATE KEY UPDATE
    deleted_by = VALUES(deleted_by),
    action     = VALUES(action),
    payload    = VALUES(payload),
    deleted_at = CURRENT_TIMESTAMP;

  -- 5) Apply deletion policy
  IF v_has_child THEN
    -- Soft delete: keep row to preserve thread structure
    UPDATE comments
       SET comment       = '[deleted]',
           is_active     = 0,
           deletion_date = NOW()
     WHERE comment_id = p_comment_id;
  ELSE
    -- Hard delete: safe because no children and we've locked the range
    DELETE FROM comments
     WHERE comment_id = p_comment_id;
  END IF;
END//
*/

--Start here
--Users create user:

CREATE PROCEDURE addUser (
    IN p_Username VARCHAR(50),
    IN p_PasswordHash VARCHAR(255),
    IN p_image VARCHAR(255),
    OUT uid INT
)
BEGIN
    -- Check for duplicates
    IF EXISTS (SELECT 1 FROM users WHERE username = p_Username) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username already exists.';
    END IF;
    INSERT INTO users (username, password, image_path)
    VALUES (p_Username, p_PasswordHash, p_image);

    SET uid = LAST_INSERT_ID();
END;

CREATE PROCEDURE userToAdmin (
    IN uid INT,
    OUT aid INT
)
BEGIN
    -- Verify user exists
    IF NOT EXISTS (SELECT 1 FROM users WHERE user_id = uid) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The specified user does not exist.';
    ELSEIF EXISTS (SELECT 1 FROM admins WHERE user_id = uid) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This user is already an admin.';
    ELSE
        INSERT INTO admins (user_id) VALUES (uid);
        SET aid = LAST_INSERT_ID();
    END IF;
END//

DELIMTER ;