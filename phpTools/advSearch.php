<?php
    require __DIR__ . '/config.php';
    $db = get_db();
    $logFile = __DIR__ . '/../logFiles/advSearch.log';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        fail(405, 'Method Not Allowed');
    }

    $genres = NULL;
    $isbn = htmlspecialchars($_GET['isbn']);
    $title = htmlspecialchars($_GET['title']);

    if (isset($_GET['genres'])) {
        $genres = $_GET['genres'];
        $genreArray = explode(',', $genres);
    }
    $genres = array_map('trim', $genres);
    $genres = array_filter($genres);
    $genres = array_map('htmlspecialchars', $genres);

    $stmt = $db->query('SELECT genre FROM genres');
    $valid = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $invalid = array_diff($genres, $valid);
    if (!empty($invalid)) {
        fail(
            400,
            ('Invalid Genres:\n' . print_r($invalid, true))
        );
    }

    $author = htmlspecialchars($_GET['author']);
?>