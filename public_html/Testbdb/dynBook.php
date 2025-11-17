<?php
    require __DIR__ . '../../phpTools/config.php';
    $db = get_db();
    $logFile = __DIR__ . '/../logFiles/dynBook.log';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        fail(405, 'Method Not Allowed');
    }

    if (empty($_GET['bid'])) {
        fail(400, 'Book ID Required');
    }
?>