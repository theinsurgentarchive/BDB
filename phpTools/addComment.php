<?php
    require_once __DIR__ . '/config.php';
    $db = get_db();
    $logFile = __DIR__ . '/../logFiles/addComment.log';

    if (!isset($_SESSION['user_id'])) {
        fail(403, "Logged In Users Only");
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'Method Not Allowed');
    }

    if (empty($_POST['book_id'])) {
        fail(400, 'Book ID Required');
    }
    $book_id = htmlspecialchars($_POST['book_id']);

    if (empty($_POST['user_id'])) {
        fail(400, 'User ID Required');
    }
    $user_id = htmlspecialchars($_POST['user_id']);

    if (empty($_POST['comment_text'])) {
        fail(400, 'Comment Cannot Be Empty');
    }
    $comment_text = htmlspecialchars($_POST['comment_text']);

    $parent_id = htmlspecialchars($_POST['parent_id'] ?? '');

    $comment_id = '';

    $query = 'CALL addComment(?, ?, ?';
    if (!empty($parent_id)) {
        $query = $query . ', ?, @new_id)';
    } else {
        $query = $query . ', @new_id)';
    }
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $book_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $comment_text, PDO::PARAM_STR);
    if (!empty($parent_id)) {
        $stmt->bindParam(4, $parent_id, PDO::PARAM_INT);
    }
    $stmt->execute();

    $r1 = $db->query('SELECT @new_id AS user_id;');
    $row = $r1->fetch(PDO::FETCH_ASSOC);
    if (!$row || !$row['comment_id']) {
        fail(500, 'Comment creation failed');
    }

    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'comment_id' => $row['comment_id']]);
?>