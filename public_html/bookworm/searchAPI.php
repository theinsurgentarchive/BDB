<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../phpTools/config.php';

$db = get_db();

if (isset($_REQUEST['booktitle'])) {
    header('Content-type: application/json');

    $title = $_REQUEST['booktitle'];
    $query = '';
    $mode = false;
    if (preg_match("/^[0-9]{13}$/",$_REQUEST['booktitle'])) {  
        $query = "
            SELECT book_id, title, author FROM books 
            WHERE isbn = ? ORDER BY title, author ASC
        ";
        $mode = true;
    } else {
        $query = "
            SELECT book_id, title, author
            FROM books
            WHERE title LIKE CONCAT(?,'%') OR author LIKE CONCAT('%',?,'%')
            ORDER BY title, author ASC
        ";
    }

    $stmt = $db->prepare($query);
    if ($mode) {
        $stmt->execute([$title]);
    } else {
        $stmt->execute([$title, $title]);
    
    }
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as &$row) {
        $bid = $row['book_id'];

        $genreStmt = $db->prepare("
            SELECT genre
            FROM bookgenres
            WHERE book_id = ?
        ");
        $genreStmt->execute([$bid]);

        $genreList = array_column($genreStmt->fetchAll(PDO::FETCH_ASSOC), 'genre');
        $row['genres'] = implode(", ", $genreList);
    }

    echo json_encode($results);
    exit;
}
?>
