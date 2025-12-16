<?php
date_default_timezone_set('America/Los_Angeles');
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$altPath = '/~bdb/images/asset/placeholder.jpg';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO(
    'mysql:host=localhost;dbname=bdb;charset=utf8mb4', // not "local"
    'bdb',
    'bhof4Raw',
    $options
);

// optional helper if you want it elsewhere
function get_db() {
    global $pdo;
    return $pdo;
}

function fail(int $code, string $msg) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(
        ['ok' => false, 'error' => $msg], JSON_UNESCAPED_SLASHES
    );
    exit;
}
?>
