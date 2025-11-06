<?php
    require __DIR__ . "/config.php";
    $logFile = __DIR__ . '/../logFiles/register.log';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'Method Not Allowed');
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $image = NULL;

    if ($username === '' || preg_match('/[^A-Za-z0-9_-]{3,32}$/', $username)) {
        error_log("Invalid username supplied", 3, $logFile);
        fail(400, 'Invalid username');
    }

    if ($password === '' || strlen($password) < 8) {
        error_log("$username: Weak or empty password", 3, $logFile);
        fail(400, 'Password must be at least 8 characters');
    }
    $passhash = password_hash($password, PASSWORD_DEFAULT);
    if ($passhash === false) {
        error_log("$username: password_hash() failed", 3, $logFile);
        fail(500, 'Unable to process password');
    }
    
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            error_log(
                "$username: Upload error code {$file['error']}", 3, $logFile
            );
            fail(400, 'Image upload failed');
        }
        if ($file['size'] > 4 * 1024 * 1024) {
            fail(400, 'Image too large (MAX 4MB)');
        }
        $info = new finfo(FILEINFO_MIME_TYPE);
        $mime = $info->file($file['tmp_name']);
        $allow = [
            'image/jpeg' => 'jpg', 'image/png' => 'png' 
        ];
        if (!isset($allowed[$mime])) {
            fail(400, 'Unsupported file type');
        }
        $ext = $allowed[$mime];

        $bname = basename(random_bytes(16));
        $fname = $bname . '.' . $ext;

        $dir = '/home/stu/bdb/images/users/';
        $dest = $dir . DIRECTORY_SEPARATOR . $fname;
        if (
            !is_uploaded_file($file['tmp_name']) ||
            !move_uploaded_file($file['tmp_name'], $dest)
        ) {
            error_log("$username: move_uploaded_file failed", 3, $logFile);
            fail(500, 'Failed to store image');
        }

        $image = 'users/' . $fname;
        error_log("$username: image upload success", 3, $logFile);
    }

    $stmt = $pdo->prepare(
        "CALL adduser(:username, :password, :image_path, @new_id)"
    );
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $passhash, PDO::PARAM_STR);
    if ($image === null) {
        $stmt->bindValue(':image_path', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':image_path', $image, PDO::PARAM_STR);
    }
    $stmt->execute();

    $r1 = $pdo->query('SELECT @new_id AS 'user_id');'
    $r2 = $r1->fetch(PDO::FETCH_ASSOC);
    if (!$row || !$row['user_id']) {
        error_log("$username: Missing new user id", 3, $logFile);
        fail(500, 'User creation failed');
    }
    $newUserID = $row['user_id'];
    
    http_response_code(201);
    header('Content-Type: application/json');
    echo json_encode(
        ['ok' => true, 'user_id' => $newUserID], JSON_UNESCAPED_SLASHES
    );
    exit;
?>