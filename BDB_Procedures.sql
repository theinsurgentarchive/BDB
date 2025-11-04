DELIMITER //
DROP PROCEDURE IF EXISTS deleteComment//
DROP PROCEDURE IF EXISTS restoreComment//
DROP PROCEDURE IF EXISTS addBook//
DROP PROCEDURE IF EXISTS formToBook//
DROP PROCEDURE IF EXISTS addForm//

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
        UPDATE comments SET user_id = NULL, comment_text = '[DELETED COMMENT]'
        WHERE comment_id = cid;
    ELSE
        SET @skip_trig = 1;
        DELETE FROM comments WHERE comment_id = cid;
        SET @skip_trig = NULL;
    END IF;
END//

CREATE PROCEDURE restoreComment(IN cid INT) BEGIN
    DECLARE C_exists INT DEFAULT 0;
    DECLARE bid INT;
    DECLARE pid INT;
    DECLARE uid INT;
    DECLARE C_text TEXT;
    DECLARE C_creation_date TIMESTAMP;
    DECLARE C_depth INT;

    SELECT 1 INTO C_exists FROM comments WHERE comment_id = cid;
    IF C_exists THEN
        SELECT user_id, comment_text INTO uid, C_text
        FROM shadowcomments WHERE comment_id = cid;

        UPDATE comments SET user_id = uid, comment_text = C_text
        WHERE comment_id = cid;
    ELSE
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

CREATE PROCEDURE addBook(
    IN n_title VARCHAR(255), IN n_author VARCHAR(255), IN n_isbn VARCHAR(13),
    IN n_published DATE, IN n_summary TEXT, IN n_genres TEXT,
    IN n_image VARCHAR(512), IN n_added INT
) BEGIN
    DECLARE missing TEXT;
    DECLARE total INT DEFAULT 0;
    DECLARE j_genres JSON;
    DECLARE bid INT;

    IF n_genres IS NULL OR n_genres = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book must have a genre';
    END IF;

    SET j_genres = CAST(
        CONCAT('["', REPLACE(COALESCE(n_genres, ''), ',', '","'), '"]')
    ) AS JSON;

    CREATE TEMPORARY TABLE t_genres (
        genre VARCHAR(16) PRIMARY KEY
    );
    INSERT INTO t_genres SELECT DISTINCT TRIM(J.genre) FROM JSON_TABLE(
        j_genres, '$[*]' COLUMNS(genre VARCHAR(16) PATH '$')
    ) AS J WHERE TRIM(J.genre) <> '';
    SELECT COUNT(*) INTO total FROM t_genres;

    SELECT GROUP_CONCAT(T.genre ORDER BY T.genre SEPARATOR ',') INTO missing
    FROM t_genres T LEFT JOIN genres G ON T.genre = G.genre
    WHERE G.genre IS NULL;
    IF missing IS NOT NULL THEN
        DROP TEMPORARY TABLE IF EXISTS t_genres;
        SET @msg = CONCAT('Unknown genre(s): ', missing);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @msg;
    END IF;
    
    SET @allow = 1;
    INSERT INTO books(
        title, author, isbn, published, summary, image_path, added_by
    ) VALUES (
        n_title, n_author, n_isbn, n_published, n_summary, n_image, n_added
    );
    SET bid = LAST_INSERT_ID();
    INSERT INTO bookgenres(book_id, genre) SELECT bid, genre FROM t_genres;
    SET @allow = NULL;
    DROP TEMPORARY TABLE IF EXISTS t_genres;
END//

CREATE PROCEDURE formToBook(IN fid INT, IN aid INT) BEGIN
    DECLARE bid INT;
    DECLARE f_title VARCHAR(255);
    DECLARE f_author VARCHAR(255);
    DECLARE f_isbn VARCHAR(13);
    DECLARE f_published DATE;
    DECLARE f_image VARCHAR(512);
    DECLARE f_summary TEXT;

    IF fid IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Form_id required';
    END IF;
    IF aid IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin_id required';
    END IF;

    SELECT title, author, isbn, published, image_path, summary INTO
    f_title, f_author, f_isbn, f_published, f_image, f_summary FROM forms
    WHERE form_id = fid;
    IF f_title IS NULL AND f_author IS NULL AND f_isbn IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Form not found';
    END IF;

    UPDATE forms SET admin_id = aid, approve_date = CURRENT_TIMESTAMP
    WHERE form_id = fid;

    SET @allow = 1;
    INSERT INTO books(
        title, author, isbn, published, image_path, summary, added_by
    ) VALUES (
        f_title, f_author, f_isbn, f_published, f_image, f_summary, fid
    );
    SET bid = LAST_INSERT_ID();
    INSERT INTO bookgenres(book_id, genre) SELECT bid, genre FROM formgenres F
    WHERE F.form_id = fid;
    SET @allow = NULL;
    DROP TEMPORARY TABLE IF EXISTS t_genres;
END//

CREATE PROCEDURE addForm(
    IN n_title VARCHAR(255), IN n_author VARCHAR(255), IN n_isbn VARCHAR(13),
    IN n_published DATE, IN n_summary TEXT, IN n_genres TEXT,
    IN n_image VARCHAR(512), IN uid INT
) BEGIN
    DECLARE missing TEXT;
    DECLARE total INT DEFAULT 0;
    DECLARE j_genres JSON;
    DECLARE fid INT;
    
    IF n_genres IS NULL OR n_genres = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Form must select genre(s)';
    END IF;

    SET j_genres = CAST(
        CONCAT('["', REPLACE(COALESCE(n_genres, ''), ',', '","'), '"]')
    ) AS JSON;

    CREATE TEMPORARY TABLE t_genres (
        genre VARCHAR(16) PRIMARY KEY
    );
    INSERT INTO t_genres SELECT DISTINCT TRIM(J.genre) FROM JSON_TABLE(
        j_genres, '$[*]' COLUMNS(genre VARCHAR(16) PATH '$')
    ) AS J WHERE TRIM(J.genre) <> '';
    SELECT COUNT(*) INTO total FROM t_genres;

    SELECT GROUP_CONCAT(T.genre ORDER BY T.genre SEPARATOR ',') INTO missing
    FROM t_genres T LEFT JOIN genres G ON T.genre = G.genre
    WHERE G.genre IS NULL;
    IF missing IS NOT NULL THEN
        DROP TEMPORARY TABLE IF EXISTS t_genres;
        SET @msg = CONCAT('Unknown genre(s): ', missing);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @msg;
    END IF;
    
    SET @allow = 1;
    INSERT INTO forms(
        title, author, isbn, published, summary, image_path, user_id
    ) VALUES (
        n_title, n_author, n_isbn, n_published, n_summary, n_image, uid
    );
    SET fid = LAST_INSERT_ID();
    INSERT INTO formgenres(form_id, genre) SELECT fid, genre FROM t_genres;
    SET @allow = NULL;
    DROP TEMPORARY TABLE IF EXISTS t_genres;
END//

DELIMITER ;