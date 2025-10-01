CREATE TABLE IF NOT EXISTS comments (
    book_id BIGINT NOT NULL,
    comment_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    parent_id BIGINT DEFAULT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment VARCHAR(MAX),
--  The Average rating is going to be a Derived Attribute
--  Average Formula: (Tallied value of ratings divided by number of ratings)
    rating TINYINT(6) NOT NULL,
    CONSTRAINT constrain_rating CHECK(rating BETWEEN 0 AND 5),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (parent_id) REFERENCES comments(comment_id)
);

CREATE TABLE IF NOT EXISTS books (
    book_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    add_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    summary VARCHAR(MAX),
    image_path VARCHAR(MAX)
--  Only allow image files to be a max size of 2048px by 2048px
);

CREATE TABLE IF NOT EXISTS users (
    user_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    image_path VARCHAR(MAX),
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(64) UNIQUE NOT NULL,
    pass_hash VARCHAR(MAX) NOT NULL,
    salt VARCHAR(MAX) NOT NULL
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    FOREIGN KEY user_id REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS forms (
    form_id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    book_name VARCHAR(MAX) NOT NULL,
    image_path VARCHAR(MAX),
    author VARCHAR(MAX) NOT NULL,
    summary VARCHAR(MAX) NOT NULL,
--  Only allow image files to be a max size of 2048px by 2048px
    FOREIGN KEY user_id REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS genres (
    genre VARCHAR(MAX) NOT NULL,
    book_id BIGINT PRIMARY KEY NOT NULL,
    FOREIGN KEY book_id REFERENCES books(book_id)
);