CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    image_path VARCHAR(512) NULL DEFAULT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    deletion_date TIMESTAMP NULL DEFAULT NULL,
    username VARCHAR(64) UNIQUE NOT NULL,
    /*Use Password Hashing for Security*/
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    promotion_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS forms (
    form_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    /*Cleanse ISBN input when preparing insert to exclude non-digits*/
    isbn VARCHAR(13) NOT NULL,
    title VARCHAR(255) NOT NULL,
    image_path VARCHAR(512) NULL DEFAULT NULL,
    published DATE NULL DEFAULT NULL,
    author VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    approve_date TIME NULL DEFAULT NULL,
    UNIQUE KEY dedupe (user_id, isbn),
    CONSTRAINT constrain_form_isbn CHECK (isbn REGEXP '^[0-9]{13}$'),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id)
);

CREATE TABLE IF NOT EXISTS books (
    book_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    isbn VARCHAR(13) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    added TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    published DATE NULL DEFAULT NULL,
    summary TEXT NOT NULL,
    image_path VARCHAR(512) DEFAULT NULL,
    added_by INT NULL DEFAULT NULL,
    author VARCHAR(255) NOT NULL,
    CONSTRAINT constrain_book_isbn CHECK (isbn REGEXP '^[0-9]{13}$'),
    FOREIGN KEY (added_by) REFERENCES forms(form_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    /*The Average rating is going to be a Derived Attribute
    Average Formula: (Tallied value of ratings divided by number of ratings)*/
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    rating INT NOT NULL,
    CONSTRAINT constrain_rating CHECK(rating BETWEEN 0 AND 5),
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    book_id INT NOT NULL,
    comment_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT NULL DEFAULT NULL,
    parent_id INT NULL DEFAULT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment_text TEXT NOT NULL,
    depth INT NOT NULL DEFAULT 0,
    deletion_date TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES comments(comment_id)
);

CREATE TABLE IF NOT EXISTS genres (
    genre VARCHAR(16) PRIMARY KEY NOT NULL
);

CREATE TABLE IF NOT EXISTS bookgenres (
    book_id INT NOT NULL,
    genre VARCHAR(16) NOT NULL,
    PRIMARY KEY (book_id, genre),
    FOREIGN KEY (genre) REFERENCES genres(genre) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS formgenres (
    form_id INT NOT NULL,
    genre VARCHAR(16) NOT NULL,
    PRIMARY KEY (form_id, genre),
    FOREIGN KEY (genre) REFERENCES genres(genre) ON DELETE CASCADE,
    FOREIGN KEY (form_id) REFERENCES forms(form_id) ON DELETE CASCADE
);

INSERT INTO genres (genre) VALUES 
    ('Adventure'), ('Sci-Fi'), ('Horror'), ('Mystery'), ('Thriller'),
    ('Romance'), ('Historical'), ('Fantasy'), ('Action'), ('Crime'),
    ('Dystopian'), ('Classic'), ('Non-Fiction');

--Username: Test password: userTest
INSERT INTO users(username, password) VALUES (
    'Test',
    '$2y$10$jzsdFb/NVamhUzXHKu/VhuLU3mWSkLJC1b6gRh0qmBr38t4UmwCE2'
);

--Need to create Procedures to insert, retrieve, and delete from this table.
--Commenter and comment text obfuscated for anonomity
CREATE TABLE IF NOT EXISTS shadowcomments (
    book_id INT NOT NULL,
    comment_id INT PRIMARY KEY NOT NULL,
    creation_date TIMESTAMP NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    comment_text TEXT NOT NULL,
    depth INT NOT NULL,
    deleted_by INT NULL,
    deletion_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    reason ENUM('USER', 'ADMIN', 'NONE') NULL,
    action ENUM('HARD', 'SOFT') NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (deleted_by) REFERENCES users(user_id) ON DELETE SET NULL
);