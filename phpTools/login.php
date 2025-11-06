<?php
    require __DIR__ . '/config.php';
    $logFile = __DIR__ . '/../logFiles/login.log';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'Method Not Allowed');
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === '' || !preg_match('/^[A-Za-z0-9_-]{6,}/', $username)) {
        error_log("Invalid username supplied", 3, $logFile);
        fail(400, 'Invalid username');
    }

    $stmt = $pdo->prepare(
        "SELECT user_id, password FROM users WHERE username = :username"
    );
    $stmt->bindParam(':username', PDO::PARAM_STR);
    $stmt->execute();
    $result = $pdo->fetch(PDO::FETCH_ASSOC);
    if (!$result || !$result['user_id'] || !$result['password']) {
        error_log("$username: Retrieve Failed", 3, $logFile);
        fail(500, 'User Retrieve failed');
    }

    if (!password_verify($password, $result['password']) {
        error_log("$username: Password Match Failed", 3, $logFile);
        fail(500, 'Login Denied: password mismatch');
    }

    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(
        ['ok' => true, 'user_id' => $result['user_id']]
    );
    exit;
?>