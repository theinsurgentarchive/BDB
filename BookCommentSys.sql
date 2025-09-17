CREATE TABLE IF NOT EXISTS comments (
    book_id BIGINT NOT NULL,
    comment_id BIGINT NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment VARCHAR(MAX),
--  The Average rating is going to be a Derived Attribute
--  Average Formula: (Tallied value of ratings divided by number of ratings)
    rating TINYINT(6) NOT NULL,
    CONSTRAINT constrain_rating CHECK(rating BETWEEN 0 AND 5),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    PRIMARY KEY (comment_id)
);

CREATE TABLE IF NOT EXISTS images (
    image_id BIGINT NOT NULL AUTO_INCREMENT,
    filename VARCHAR(MAX) NOT NULL,
    path_ VARCHAR(MAX) NOT NULL,
    PRIMARY KEY (image_id)
);

CREATE TABLE IF NOT EXISTS books (
    book_id BIGINT NOT NULL AUTO_INCREMENT,
    add_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    summary VARCHAR(MAX),
    image_id BIGINT NOT NULL,
--  Only allow image files to be a max size of 2048px by 2048px
    FOREIGN KEY image_id REFERENCES images(image_id),
    PRIMARY KEY (book_id)
);

CREATE TABLE IF NOT EXISTS users (
    user_id BIGINT NOT NULL AUTO_INCREMENT,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(64) UNIQUE NOT NULL,
    pass_hash VARCHAR(MAX) NOT NULL,
    salt VARCHAR(MAX) NOT NULL,
    PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id BIGINT NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    FOREIGN KEY user_id REFERENCES users(user_id),
    PRIMARY KEY (admin_id)
);

CREATE TABLE IF NOT EXISTS forms (
    form_id BIGINT NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    book_name VARCHAR(MAX) NOT NULL,
    author VARCHAR(MAX) NOT NULL,
    summary VARCHAR(MAX) NOT NULL,
    image_id BIGINT NOT NULL,
--  Only allow image files to be a max size of 2048px by 2048px
    FOREIGN KEY image_id REFERENCES images(image_id),
    FOREIGN KEY user_id REFERENCES users(user_id),
    PRIMARY KEY (form_id)
);