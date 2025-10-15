--Users Insert
INSERT INTO users(username, password) VALUES 
(), (), (), ();
--Admins Insert
INSERT INTO admins(user_id) SELECT user_id FROM users 
WHERE username = "";

--Books Insert
--Example: ("Test", "YYYY-MM_DD", "This is an Example, "Example Author", 12041024)
INSERT INTO books (name, published, summary, author, isbn) VALUES
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
(),
();

--BookGenres Inserts
INSERT INTO bookGenres (book_id, genre) VALUES
((SELECT book_id FROM books WHERE isbn = ), "");