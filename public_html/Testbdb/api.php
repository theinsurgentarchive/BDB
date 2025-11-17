<?php

require __DIR__ . '/../../phpTools/config.php';

$action = $_GET['action'] ?? ''; 

switch ($action) {
    case 'login':
        require __DIR__ . '/../../phpTools/login.php';
        break;

    case 'register':
        require __DIR__ . '/../../phpTools/register.php';
        break;

    default:
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Invalid or missing action']);
        exit;
}
?>
