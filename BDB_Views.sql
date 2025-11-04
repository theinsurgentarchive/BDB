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
