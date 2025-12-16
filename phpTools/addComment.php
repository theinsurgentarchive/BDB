<?php
    require_once __DIR__ . '/config.php';
    $db = get_db();

    function renderComments(
        $parentId,
        $commentsByParent,
        $shadowById,
        $user_id,
        $book_id,
        $altPath,
        $depth = 0
    ) {
        $db = get_db();
        if (!isset($commentsByParent[$parentId])) return '';
    
        $html = '';
    
        // replies (non-top-level) are wrapped in a replyGroup
        if ($parentId !== null) {
            $html .= "<div class='replyGroup'>";
        }
    
        foreach ($commentsByParent[$parentId] as $c) {
            $sc = $shadowById[$c['comment_id']] ?? null;
            // base class is comment-card; replies also get class="reply"
            $classes = 'comment-card';
            if ($depth > 0) {
                $classes .= ' reply';
            }
            $html .= "<section id='cid" . $c['comment_id'] . "'></section>";
            $html .= "<div class='{$classes}' id='{$c['comment_id']}' style='margin-left:" . (20 * $depth) . "px'>";
            $html .= "<a href='/~bdb/bookworm/profile.php?uid=" . $c['user_id'] . "'>";
            $html .= "<img class='commentPic' src='" . htmlspecialchars($c['image_path'] ?? $altPath) . "' alt='Missing_Image'>";
            $html .= "</a>";
            $html .= "<h4 class='commentUser'>" . htmlspecialchars($c['username'] ?? '[Removed Content]') . "</h4>";
            $html .= "<div class='commentText'>";
            if ($c['user_id'] === $user_id) {
                $html .= "<p contenteditable='true' style='white-space: pre-wrap'>" . $c['comment_text'] . "</p>";
            } else {
                $html .= "<p style='white-space: pre-wrap'>" . $c['comment_text'] . "</p>";
            }
            $html .= "</div>";
            $html .= "<span class='commentDate' data-date='" . htmlspecialchars($c['creation_date']) . "'>" . htmlspecialchars($c['creation_date']) . "</span>";
        
            if (!empty($c["modified_date"])) {
                $html .= "<label><b>Edited On: </b></label>";
                $html .= "<span class='commentDate' data-date='" . htmlspecialchars($c['modified_date']) . "'>" . htmlspecialchars($c['modified_date']) . "</span>";
            }
        
            $html .= "<div class='commentActions'>";
            //Reply Button
            if ($depth < 5 && !empty($user_id)) {
                $html .= "<button class='replyButton' data-id='" . $c['comment_id'] . "'>Reply</button>";
                $html .= "<div class='replyForm hidden' data-id='" . $c['comment_id'] . "'>";
                $html .= "<textarea rows='5' cols='50' name='commentText' placeholder='Write a reply...'></textarea><br>";
                $html .= "<button class='replySubmit' data-bid='$book_id' data-uid='$user_id' data-pid='{$c["comment_id"]}'>Submit</button>";
                $html .= "<button class='replyCancel' data-id='" . $c['comment_id'] . "'>Cancel</button>";
                $html .= "</div>";
            }

            if ($c['user_id'] === $user_id) {
                //Delete Button
                $html .= "<button class='deleteButton'data-cid='{$c['comment_id']}'data-reason='USER'>Delete</button>";
                //edit Button
            } else if (!empty($sc) && $sc['user_id'] === $user_id) {
                //Restore Button
                $html .= "<button class='restoreButton'data-cid='" . $sc['comment_id'] . "'>Restore</button>";
            }
            $html .= "</div>";
        
            // recurse for this comment's children
            $html .= renderComments(
                $c['comment_id'],
                $commentsByParent,
                $shadowById,
                $user_id,
                $book_id,
                $altPath,
                $depth + 1
            );
        
            $html .= "</div>"; // end .comment-card
        }
    
        if ($parentId !== null) {
            $html .= "</div>"; // end .replyGroup
        }
    
        return $html;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fail(405, 'Method Not Allowed');
    }

    if (empty($_GET['t'])) {
        fail(400, 'Action Type Required');
    }
    $type = null;
    if ($_GET['t'] === 'f') {
        $type = 0;
    } else if ($_GET['t'] === 'c') {
        $type = 1;
    } else if ($_GET['t'] === 'd') {
        $type = 2;
    } else if ($_GET['t'] === 'r') {
        $type = 3;
    } else if ($_GET['t'] === 'e') {
        $type = 4;
    } else {
        fail(400, 'Action Type Can Only be \'(f)etch\', \'\'(c)reate\', \'(d)elete\', \'(r)estore\', or \'(e)dit');
    }

    if ($type === 0) {
        if (empty($_POST['bid'])) {
            fail(400, 'Book ID Required');
        }
        $book_id = $_POST['bid'];
        $offset = (empty($_POST['page']) ? 1 : $_POST['page']);
        
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM usercomments WHERE book_id = ? AND depth = 0
        ');
        $stmt->execute([$book_id]);
        $totalTop = (int)$stmt->fetchColumn();
        $total = max(1, ceil($totalTop / 10));
        if ($offset < 1) $offset = 1;
        if ($offset > $total) $offset = $total;

        $offset = ($offset - 1) * 10;
        $user_id = $_SESSION['user_id'] ?? null;
        $stmt = $db->prepare('
            SELECT * FROM usercomments WHERE book_id = ? AND depth = 0
            ORDER BY creation_date DESC LIMIT 10 OFFSET ?
        ');
        $stmt->execute([$book_id, $offset]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cids = array_column($comments, 'comment_id');
        if (!$cids) {
            echo "";
            exit;
        }
        
        $all = $comments;
        $queue = $cids;
        while (!empty($queue)) {
            $ph = implode(', ', array_fill(0, count($queue), '?'));
            $sql = "SELECT * FROM usercomments WHERE parent_id IN ($ph)";

            $stmt = $db->prepare($sql);
            $stmt->execute($queue);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if (empty($rows)) break;
        
            $all = array_merge($all, $rows);
            $queue = array_column($rows, 'comment_id'); // search deeper
        }

        $comments = $all;
        $commentsByParent = [];
        foreach ($comments as $c) {
            $pid = $c["parent_id"] ?? null;
            $commentsByParent[$pid][] = $c;
        }

        $stmt = $db->prepare("
            SELECT *
            FROM shadowcomments
            WHERE book_id = ?
              AND user_id = ?
        ");
        $stmt->execute([$book_id, $user_id]);
        $shadowRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $shadowById = [];
        foreach ($shadowRows as $row) {
            $shadowById[$row['comment_id']] = $row;
        }

        $comm = renderComments(
            null,
            $commentsByParent,
            $shadowById,
            $user_id,
            $book_id,
            $altPath
        );

        echo "<!--PAGES:$total-->";
        echo $comm;
    }
    
    if ($type === 1) {
        if (!isset($_SESSION['user_id'])) {
            fail(403, "Logged-In Users Only");
        }
        if (empty($_POST['bid'])) {
            fail(400, 'Book ID Required');
        }
        $book_id = $_POST['bid'];

        if (empty($_POST['uid'])) {
            fail(400, 'User ID Required');
        }

        $user_id = $_POST['uid'];

        if (empty($_POST['commentText'])) {
            fail(400, 'Comment Cannot Be Empty');
        }
        $comment_text = htmlspecialchars($_POST['commentText']);

        $parent_id = trim($_POST['pid'] ?? '');

        $comment_id = '';

        $query = 'CALL addComment(?, ?, ?, ?, @new_id)';
        try {
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $book_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $comment_text, PDO::PARAM_STR);
            if ($parent_id === '') {
                $stmt->bindValue(4, null, PDO::PARAM_NULL);
            } else {
                $pid = (int)$parent_id;
                $stmt->bindParam(4, $pid, PDO::PARAM_INT);
            }

            $stmt->execute();

            $r1 = $db->query('SELECT @new_id AS comment_id;');
            $row = $r1->fetch(PDO::FETCH_ASSOC);
            if (!$row || !$row['comment_id']) {
                fail(500, 'Comment creation failed');
            }

            http_response_code(200);
            header('Content-Type: application/json');
            if (empty($parent_id)) {
                echo json_encode([
                    'ok' => true,
                    'user_id'=> $user_id,
                    'book_id'=> $book_id,
                    'comment_id' => $comment_text,
                    'parent_id' => 'NULL'
                ]);
            } else {
                echo json_encode([
                    'ok' => true,
                    'user_id'=> $user_id,
                    'book_id'=> $book_id,
                    'comment_id' => $comment_text,
                    'parent_id' => $parent_id
                ]);
            }
        } catch (PDOException $e) {
            fail(500, $e->getMessage());
        }
    }
    if ($type === 2) {
        if (!isset($_SESSION['user_id'])) {
            fail(403, "Logged-In Users Only");
        }
        if (empty($_POST['cid'])) {
            fail(400, 'Comment ID Required');
        }
        $comment_id = (int)$_POST['cid'];

        // Get owner from DB, not from POST
        $stmt = $db->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
        $stmt->execute([$comment_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            fail(500, "Comment Not Found");
        }

        $commentOwner = (int)$row['user_id'];
        $currentUser = (int)$_SESSION['user_id'];

        if ($commentOwner !== $currentUser) {
            fail(403, "Must be Owner to Delete Comment");
        }

        if (empty($_POST['reason'])) {
            fail(400, 'Reason Required');
        }
        $reason = $_POST['reason'];

        $query = 'CALL deleteComment(?, ?, ?)';
        try {
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $comment_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $currentUser, PDO::PARAM_INT);
            $stmt->bindParam(3, $reason, PDO::PARAM_STR);
            $stmt->execute();

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'text' => 'Comment Successfully Deleted'
            ]);
        } catch (PDOException $e) {
            fail(500, $e->getMessage());
        }
    }
    if ($type === 3) {
        if (!isset($_SESSION['user_id'])) {
            fail(403, "Logged-In Users Only");
        }
        if (empty($_POST['cid'])) {
            fail(400, 'Comment ID Required');
        }
        $comment_id = (int)$_POST['cid'];
        try {
            $stmt = $db->prepare('CALL restoreComment(?)');
            $stmt->bindParam(1, $comment_id, PDO::PARAM_INT);
            $stmt->execute();
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'text' => 'Comment Successfully Restored'
            ]);
        } catch (PDOException $e) {
            fail(500, $e->getMessage());
        }
    }
    if ($type === 4) {
        if (empty($_POST['comment_id'])) {
            fail(400, 'Comment ID Required');
        }
        $comment_id = $_POST['comment_id'];

        if (empty($_POST['commentText'])) {
            fail(400, 'Comment Cannot Be Empty');
        }
        $comment_text = htmlspecialchars($_POST['commentText']);

        $stmt = $db->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
        $stmt->execute([$comment_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            fail(500, "Comment Not Found");
        }

        $commentOwner = (int)$row['user_id'];
        $currentUser = (int)$_SESSION['user_id'];

        if ($commentOwner !== $currentUser) {
            fail(403, "Must be Owner to Edit Comment");
        }

        try {
            $stmt = $db->prepare('
                UPDATE comments SET commentText = ? WHERE comment_id = ?
            ');
            $stmt->bindParam(1, $comment_text, PDO::PARAM_STR);
            $stmt->bindParam(2, $comment_id, PDO::PARAM_INT);
            $stmt->execute();
            http_response_code(200);
            header('Content-Type:application/json');
            echo json_encode([
                'ok' => true,
                'text' => 'Comment Successfully Updated'
            ]);
        } catch (PDOException $e) {
            fail(500, $e->getMessage());
        }
    }
?>