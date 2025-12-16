<?php
require __DIR__ . '/../../phpTools/config.php';

$back = $_SERVER['HTTP_REFERER'] ?? '/~bdb/bookworm/homepage.php';
if (preg_match('/\~bdb\/bookworm\/adminTool\.php/', $back)) {
    $back = '/~bdb/bookworm/homepage.php';
}
if (preg_match('/\~bdb\/bookworm\/form\.php/', $back)) {
    $back = '/~bdb/bookworm/homepage.php';
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();
header("Location: " . $back);
exit;
