<?php
    require __DIR__ . '/config.php';
    $db = get_db();
    $logFile = __DIR__ . '/../logFiles/simpleSearch.log';

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        fail(405, 'Method Not Allowed');
    }
    //Insert Search Bar Code here
?>