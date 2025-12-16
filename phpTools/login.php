<?php
require_once __DIR__ . '/config.php';
$db = get_db();
$logFile = __DIR__ . '/../logFiles/login.log';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail(405, 'Method Not Allowed');
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || !preg_match('/^[A-Za-z0-9_-]{6,32}$/', $username)) {
    //error_log("Invalid username supplied", 3, $logFile);
    fail(400, 'Invalid username');
}

if ($password === '' || strlen($password) < 6) {
    //error_log("$username: Weak or empty password", 3, $logFile);
    fail(400, 'Invalid password');
}

$stmt = $db->prepare("
    SELECT 
        u.user_id, 
        u.password, 
        a.admin_id
    FROM users AS u
    LEFT JOIN admins AS a ON u.user_id = a.user_id
    WHERE u.username = :username AND u.is_active = 1
");
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    //error_log("$username: not found", 3, $logFile);
    fail(401, 'Invalid credentials');
}

if (!password_verify($password, $user['password'])) {
    //error_log("$username: incorrect password", 3, $logFile);
    fail(401, 'Invalid credentials');
}

$isAdmin = !empty($user['admin_id']);
$role = $isAdmin ? 'admin' : 'user';

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $username;
$_SESSION['role'] = $role;

//error_log("$username logged in as $role", 3, $logFile);

http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'user_id' => $user['user_id'],
    'username' => $username,
    'role' => $role
]);
exit;
?>