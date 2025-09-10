CREATE TABLE IF NOT EXISTS comments (
    book_id BIGINT NOT NULL,
    inner_id BIGINT NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    comment VARCHAR(MAX),
--  The Average rating is going to be a Derived Attribute
--  Average Formula: (Tallied value of ratings divided by number of ratings)
    rating TINYINT(6) NOT NULL,
    CONSTRAINT constrain_rating CHECK(rating BETWEEN 0 AND 5),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS images (
    id BIGINT NOT NULL AUTO_INCREMENT,
    filename VARCHAR(MAX) NOT NULL,
    path_ VARCHAR(MAX) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS books (
    id BIGINT NOT NULL AUTO_INCREMENT,
    add_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    summary VARCHAR(MAX),
    image_id BIGINT NOT NULL,
--  Only allow image files to be a max size of 2048px by 2048px
    FOREIGN KEY image_id REFERENCES images(id),
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS users (
    id BIGINT NOT NULL AUTO_INCREMENT,
    creation_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(64) UNIQUE NOT NULL,
    pass_hash VARCHAR(MAX) NOT NULL,
    salt VARCHAR(MAX) NOT NULL,
    admin BIT NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS forms (
    inner_id BIGINT NOT NULL AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    book_name VARCHAR(MAX) NOT NULL,
    author VARCHAR(MAX) NOT NULL,
    summary VARCHAR(MAX) NOT NULL,
    image_id BIGINT NOT NULL,
--  Only allow image files to be a max size of 2048px by 2048px
    FOREIGN KEY image_id REFERENCES images(id),
    FOREIGN KEY user_id REFERENCES users(id)
);