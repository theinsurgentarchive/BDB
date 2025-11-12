DROP VIEW IF EXISTS randbooks;
DROP VIEW IF EXISTS randbookgenres;


-- Homepage

-- random book generator for the homepage
CREATE OR REPLACE VIEW randbooks AS
SELECT
  b.book_id,
  b.title  AS title,
  b.author,
  b.image_path
FROM books b
ORDER BY RAND()
LIMIT 10;

-- top three genres by average ratings for the homepage
CREATE OR REPLACE VIEW randbookgenres AS
SELECT
  g.genre,
  ROUND(AVG(r.rating), 2) AS avg_rating
FROM ratings AS r
JOIN bookgenres AS bg ON bg.book_id = r.book_id
JOIN genres      AS g  ON g.genre    = bg.genre
GROUP BY g.genre
ORDER BY avg_rating DESC, g.genre
LIMIT 5;
-- top 3 book (top of the page)

create or replace view topthreebooks as select 
    b.book_id, b.name, b.author, AVG(r.rating) as avg_rating, COUNT(r.rating_id) AS totalratings 
from books as b natural join ratings as r 
group by b.book_id 
order by totalratings desc, avg_rating desc 
limit 3;


-- Most Commented Books 

create or replace view mostcommentedbooks SELECT 
    b.book_id, b.name, b.author,
    COUNT(c.comment_id) AS total_comments
FROM books b
JOIN comments c ON b.book_id = c.book_id
GROUP BY b.book_id, b.name, b.author
ORDER BY total_comments DESC
LIMIT 3;

-- Most Active Users
create or replace view mostactiveusers SELECT 
    u.username,
    COUNT(r.rating_id) + COUNT(c.comment_id) AS total_activity
FROM users u
LEFT JOIN ratings r ON u.user_id = r.user_id
LEFT JOIN comments c ON u.user_id = c.user_id
GROUP BY u.username
ORDER BY total_activity DESC
LIMIT 5;
