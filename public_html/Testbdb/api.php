<?php

require '/home/stu/bdb/phpTools/config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        require '/home/stu/bdb/phpTools/login.php';
        break;

    case 'register':
        require '/home/stu/bdb/phpTools/register.php';
        break;

    default:
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Invalid or missing action']);
        exit;
}
