<?php
    require_once __DIR__ . "/config.php";
    $db = get_db();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'Method Not Allowed');
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $image = NULL;

    // tightened regex to enforce full 6–32 chars match (optional but safer)
    if ($username === '' || !preg_match('/^[A-Za-z0-9_-]{6,32}$/', $username)) {
        fail(400, 'Invalid username');
    }

    if ($password === '' || strlen($password) < 6) {
        fail(400, 'Password must be at least 6 characters');
    }
    if (strlen($password) > 20) {
        fail(400, 'Password must be under 20 characters long');
    }

    $passhash = password_hash($password, PASSWORD_DEFAULT);
    if ($passhash === false) {
        fail(500, 'Unable to process password');
    }
    
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            fail(400, 'Image upload failed');
        }
        if ($file['size'] > 4 * 1024 * 1024) {
            fail(400, 'Image too large (MAX 4MB)');
        }

        $info = new finfo(FILEINFO_MIME_TYPE);
        $mime = $info->file($file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png'
        ];
        if (!isset($allowed[$mime])) {
            fail(400, 'Unsupported file type');
        }
        $ext = $allowed[$mime];

        $bname = bin2hex(random_bytes(16));
        $fname = $bname . '.' . $ext;

        $dir = '/home/stu/bdb/public_html/images/users';
        $dest = $dir . '/' . $fname;
        if (
            !is_uploaded_file($file['tmp_name']) ||
            !move_uploaded_file($file['tmp_name'], $dest)
        ) {
            fail(500, 'Failed to store image');
        }

        $image = '/~bdb/images/users/' . $fname;
    }

    $stmt = $db->prepare(
        "CALL adduser(:username, :password, :image_path, @new_id)"
    );
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $passhash, PDO::PARAM_STR);

    $imageParam = $image; 
    if ($imageParam === null) {
        $stmt->bindValue(':image_path', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(':image_path', $imageParam, PDO::PARAM_STR);
    }
    $stmt->execute();

    $r1 = $db->query('SELECT @new_id AS user_id;');
    $row = $r1->fetch(PDO::FETCH_ASSOC);
    if (!$row || !$row['user_id']) {
        fail(500, 'User creation failed');
    }
    $newUserId = $row['user_id'];
    
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => true,
        'user_id' => $newUserId,
        'username' => $username,
        'role' => 'user'
    ]);
    exit;
?>
