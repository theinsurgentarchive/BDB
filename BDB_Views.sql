-- top 3 book (top of the page)

select 
    b.book_id, b.name, b.author, AVG(r.rating) as avg_rating, COUNT(r.rating_id) AS totalratings 
from books as b natural join ratings as r 
group by b.book_id 
order by totalratings desc, avg_rating desc 
limit 3;


-- Most Commented Books 

SELECT 
    b.book_id, b.name, b.author,
    COUNT(c.comment_id) AS total_comments
FROM books b
JOIN comments c ON b.book_id = c.book_id
GROUP BY b.book_id, b.name, b.author
ORDER BY total_comments DESC
LIMIT 3;

-- Most Active Users
SELECT 
    u.username,
    COUNT(r.rating_id) + COUNT(c.comment_id) AS total_activity
FROM users u
LEFT JOIN ratings r ON u.user_id = r.user_id
LEFT JOIN comments c ON u.user_id = c.user_id
GROUP BY u.username
ORDER BY total_activity DESC
LIMIT 5;