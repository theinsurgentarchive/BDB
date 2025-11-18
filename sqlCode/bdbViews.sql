DROP VIEW IF EXISTS randbooks;
DROP VIEW IF EXISTS randbookgenres;
DROP VIEW IF EXISTS topthreebooks;
DROP VIEW IF EXISTS toptwentybooks;
DROP VIEW IF EXISTS usercomments;

-- contains the username, image path, and comment data of every commenter
CREATE OR REPLACE VIEW usercomments AS
SELECT U.username, U.image_path, C.* FROM comments C
JOIN users U ON C.user_id = U.user_id;

-- random book generator for the homepage
CREATE OR REPLACE VIEW randbooks AS
SELECT B.book_id, B.title, B.author, B.image_path FROM books B ORDER BY RAND()
LIMIT 10;

-- top five genres by average ratings for the homepage
CREATE OR REPLACE VIEW randbookgenres AS
SELECT G.genre, ROUND(AVG(R.rating), 2) AS avg_rating FROM ratings AS R
JOIN bookgenres AS BG ON BG.book_id = R.book_id
JOIN genres AS G ON G.genre = BG.genre
GROUP BY G.genre ORDER BY avg_rating DESC, G.genre LIMIT 5;

-- top 3 book (top of the page)
CREATE OR REPLACE VIEW topthreebooks AS 
SELECT B.book_id, B.title,B.author,
AVG(R.rating) AS avg_rating, COUNT(R.rating_id) AS totalratings 
FROM books AS B JOIN ratings AS R ON B.book_id = R.book_id
GROUP BY B.book_id ORDER BY totalratings DESC, avg_rating DESC LIMIT 3;


--- Top 20 page (main ranking)
CREATE OR REPLACE VIEW toptwentybooks AS
select B.book_id, B.title,B.author, B.summary,
AVG(R.rating) AS avg_rating, COUNT(R.rating_id) AS totalratings 
from books AS B JOIN ratings AS R ON B.book_id = R.book_id
GROUP BY B.book_id ORDER BY totalratings DESC, avg_rating DESC LIMIT 20;