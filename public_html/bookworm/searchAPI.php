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

    $title = $_REQUEST['booktitle'] . '%';

    $query = $db->prepare("
        SELECT book_id, title, author
        FROM books
        WHERE title LIKE ? OR author LIKE ?
    ");
    $query->execute([$title, $title]);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as &$row) {
        $bid = $row['book_id'];

        $genreQuery = $db->prepare("
            SELECT genre
            FROM bookgenres
            WHERE book_id = ?
        ");
        $genreQuery->execute([$bid]);

        $genreList = array_column($genreQuery->fetchAll(PDO::FETCH_ASSOC), 'genre');
        $row['genres'] = implode("|", $genreList);
    }

    echo json_encode($results);
    exit;
}
