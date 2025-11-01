-- random book generator for the homepage
SELECT
  b.book_id,
  b.name  AS title,
  b.author,
  b.image_path
FROM books b
ORDER BY RAND()
LIMIT 10;   

-- top three genres by avverage ratings for the homepage
SELECT
  g.genre,
  ROUND(AVG(r.rating), 2) AS avg_rating
FROM ratings AS r
JOIN bookGenres AS bg ON bg.book_id = r.book_id
JOIN genres      AS g  ON g.genre    = bg.genre
GROUP BY g.genre
ORDER BY avg_rating DESC, g.genre
LIMIT 5;


