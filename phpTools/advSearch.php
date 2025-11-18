<?php
    require __DIR__ . '/config.php';
    $db = get_db();

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        fail(405, 'Method Not Allowed');
    }
    $isbn = htmlspecialchars($_GET['isbn']);
    $title = htmlspecialchars($_GET['title']);
    $genres = htmlspecialchars($_GET['genres']);
    $author = htmlspecialchars($_GET['author']);
    
    //Insert Search Bar Code heres
?>