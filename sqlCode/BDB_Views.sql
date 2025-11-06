DROP VIEW IF EXISTS randbooks;
DROP VIEW IF EXISTS randbookgenres;
DROP VIEW IF EXISTS topthreebooks;
DROP VIEW IF EXISTS toptwentybooks;

-- Homepage

-- random book generator for the homepage
CREATE OR REPLACE VIEW randbooks AS
SELECT
  b.book_id,
  b.title,
  b.author,
  b.image_path
FROM books b
ORDER BY RAND()
LIMIT 10;

-- top three genres by avverage ratings for the homepage
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
create or replace view topthreebooks as 
select
 b.book_id, 
 b.title,
 b.author, 
 AVG(r.rating) as avg_rating, 
 COUNT(r.rating_id) AS totalratings 
from books as b natural join ratings as r 
group by b.book_id 
order by totalratings desc, avg_rating desc 
limit 3;


--- Top 20 page (main ranking)
CREATE OR REPLACE VIEW toptwentybooks AS
select
 b.book_id, 
 b.title,
 b.author, 
 b.summary,
 AVG(r.rating) as avg_rating, 
 COUNT(r.rating_id) AS totalratings 
from books as b natural join ratings as r 
group by b.book_id 
order by totalratings desc, avg_rating desc 
limit 20;