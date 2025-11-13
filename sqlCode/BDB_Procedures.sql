DELIMITER //
DROP PROCEDURE IF EXISTS deleteComment//
DROP PROCEDURE IF EXISTS restoreComment//
DROP PROCEDURE IF EXISTS addBook//
DROP PROCEDURE IF EXISTS formToBook//
DROP PROCEDURE IF EXISTS addForm//
DROP PROCEDURE IF EXISTS splitGenreCSV//
DROP PROCEDURE IF EXISTS addUser//
DROP PROCEDURE IF EXISTS userToAdmin//
DROP PROCEDURE IF EXISTS topActiveUsers//

CREATE PROCEDURE splitGenreCSV(IN csv TEXT) BEGIN
    DECLARE list TEXT;
    DECLARE gen VARCHAR(16);
    DECLARE pos INT;

    DROP TEMPORARY TABLE IF EXISTS t_genres;
    CREATE TEMPORARY TABLE t_genres (
        genre VARCHAR(16) PRIMARY KEY
    );
    SET list = TRIM(BOTH ',' FROM COALESCE(csv, ''));
    
    WHILE list IS NOT NULL AND list <> '' DO
        SET pos = LOCATE(',', list);

        IF pos = 0 THEN
            SET gen = TRIM(list);
            SET list = '';
        ELSE
            SET gen = TRIM(SUBSTRING(list, 1, pos - 1));
            SET list = SUBSTRING(list, pos + 1);
        END IF;

        IF gen <> '' THEN
            INSERT IGNORE INTO t_genres(genre) VALUES (gen);
        END IF;
    END WHILE;
END//

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
    DECLARE A CHAR(4);

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid FOR UPDATE
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 
        'Comment not found';
    END IF;

    SELECT EXISTS (
        SELECT 1 FROM comments WHERE parent_id = cid 
    ) INTO has_child;
    
    IF has_child THEN
        SET A = 'SOFT';
    ELSE
        SET A = 'HARD';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM comments WHERE comment_id = cid AND deletion_date IS NULL
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
    IN n_image VARCHAR(512), IN n_added INT, OUT bid INT
) BEGIN
    DECLARE missing TEXT;
    DECLARE total INT DEFAULT 0;
    DECLARE j_genres JSON;
    DECLARE new_id INT;

    IF n_genres IS NULL OR n_genres = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book must have a genre';
    END IF;

    CALL splitGenreCSV(n_genres);
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
    SET new_id = LAST_INSERT_ID();
    INSERT INTO bookgenres(book_id, genre) SELECT new_id, genre FROM t_genres;
    SET @allow = NULL;

    DROP TEMPORARY TABLE IF EXISTS t_genres;
    SET bid = new_id;
END//

CREATE PROCEDURE formToBook(IN fid INT, IN aid INT, OUT bid INT) BEGIN
    DECLARE new_id INT;
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
    SET new_id = LAST_INSERT_ID();
    INSERT INTO bookgenres(book_id, genre)
    SELECT new_id, genre FROM formgenres F WHERE F.form_id = fid;
    SET @allow = NULL;

    DROP TEMPORARY TABLE IF EXISTS t_genres;
    SET bid = new_id;
END//

CREATE PROCEDURE addForm(
    IN n_title VARCHAR(255), IN n_author VARCHAR(255), IN n_isbn VARCHAR(13),
    IN n_published DATE, IN n_summary TEXT, IN n_genres TEXT,
    IN n_image VARCHAR(512), IN uid INT, OUT fid INT
) BEGIN
    DECLARE missing TEXT;
    DECLARE total INT DEFAULT 0;
    DECLARE j_genres JSON;
    DECLARE new_id INT;
    
    IF n_genres IS NULL OR n_genres = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Form must select genre(s)';
    END IF;

    CALL splitGenreCSV(n_genres);
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
    SET new_id = LAST_INSERT_ID();
    INSERT INTO formgenres(form_id, genre) SELECT new_id, genre FROM t_genres;
    SET @allow = NULL;

    DROP TEMPORARY TABLE IF EXISTS t_genres;
    SET fid = new_id;
END//

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


create procedure topActiveUsers(
    in days_interval int
)
begin

    /* calculates which users have been most active withing the past x days. Activity is defined as the sum of comments and ratings a user has made */

    select 
        u.user_id,
        u.username,
        count(distinct c.comment_id) as total_comments,
        count(distinct r.rating_id) as total_ratings,
        (count(distinct c.comment_id) + count(distinct r.rating_id)) as total_activity
    from users u 
    left join comments c
        on u.user_id = c.user_id 
        and c.creation_date >= date_sub(now(), interval days_interval day)
    left join ratings r 
        on u.user_id = r.user_id 
        and r.creation_date >= date_sub(now(), interval days_interval day)
    group by u.user_id, u.username
    order by total_activity desc
    limit 10;
end//

DELIMITER ;