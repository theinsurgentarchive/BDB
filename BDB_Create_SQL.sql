CREATE TABLE IF NOT EXISTS users (
    user_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    image_path VARCHAR(1024),
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(64) UNIQUE NOT NULL,
    pass_hash VARCHAR(1024) NOT NULL,
    salt VARCHAR(1024) NOT NULL
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS forms (
    form_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    book_name TEXT NOT NULL,
    image_path VARCHAR(1024),
    author VARCHAR(256) NOT NULL,
    summary TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS books (
    book_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    added TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    published TIMESTAMP NULL DEFAULT NULL,
    summary TEXT,
    image_path VARCHAR(1024),
    created_by BIGINT DEFAULT NULL,
    author VARCHAR(256),
    FOREIGN KEY (created_by) REFERENCES forms(form_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS comments (
    book_id BIGINT NOT NULL,
    comment_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    parent_id BIGINT DEFAULT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment TEXT,
--  The Average rating is going to be a Derived Attribute
--  Average Formula: (Tallied value of ratings divided by number of ratings)
    rating TINYINT NOT NULL,
    CONSTRAINT constrain_rating CHECK(rating BETWEEN 0 AND 5),
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS genres (
    genre VARCHAR(256) NOT NULL,
    book_id BIGINT NOT NULL,
    PRIMARY KEY (genre, book_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);