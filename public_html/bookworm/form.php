<?php
require __DIR__ . '/../../phpTools/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$db       = get_db();
$userId   = (int)$_SESSION['user_id'];
$isAdmin  = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$insertMsg = "";
$adminMsg  = "";

// ---------- ADMIN: APPROVE / DENY REQUEST ----------
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve: use formToBook(fid, aid, OUT bid)
    if (isset($_POST['approve_form_id'])) {
        $formId = (int)$_POST['approve_form_id'];

        try {
            // Find admin_id for this logged-in admin
            $admStmt = $db->prepare(
                "SELECT admin_id FROM admins WHERE user_id = :uid"
            );
            $admStmt->execute([':uid' => $userId]);
            $adm = $admStmt->fetch(PDO::FETCH_ASSOC);

            if (!$adm) {
                $adminMsg = "You are not registered as an admin.";
            } else {
                $adminId = (int)$adm['admin_id'];

                // Call stored procedure formToBook
                $call = $db->prepare(
                    "CALL formToBook(:fid, :aid, @new_book_id)"
                );
                $call->execute([
                    ':fid' => $formId,
                    ':aid' => $adminId,
                ]);

                $row = $db->query("SELECT @new_book_id AS book_id")
                    ->fetch(PDO::FETCH_ASSOC);

                if ($row && $row['book_id']) {
                    $adminMsg = "Request #{$formId} approved (book ID " . (int)$row['book_id'] . ").";
                } else {

//Needs to be an error statement
                
                }
            }
        } catch (Exception $e) {
            $adminMsg = "Error approving request: " . $e->getMessage();
        }
    }

    // Deny: delete form + formgenres
    if (isset($_POST['deny_form_id'])) {
        $formId = (int)$_POST['deny_form_id'];

        try {
            $db->beginTransaction();

            $delGenres = $db->prepare(
                "DELETE FROM formgenres WHERE form_id = :form_id"
            );
            $delGenres->execute([':form_id' => $formId]);

            $delForm = $db->prepare(
                "DELETE FROM forms WHERE form_id = :form_id"
            );
            $delForm->execute([':form_id' => $formId]);

            $db->commit();
            $adminMsg = "Request #{$formId} has been denied and removed.";
        } catch (Exception $e) {
            $db->rollBack();
            $adminMsg = "Error denying request: " . $e->getMessage();
        }
    }
}

// ---------- HANDLE NEW REQUEST SUBMIT (users + admins) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_submit'])) {
    $title_data   = htmlspecialchars(trim($_POST['title_data']   ?? ''));
    $author_data  = htmlspecialchars(trim($_POST['author_data']  ?? ''));
    $isbn_data    = htmlspecialchars(trim($_POST['isbn_data']    ?? ''));
    $publish_data = htmlspecialchars($_POST['publish_data']      ?? '');
    $summary_data = htmlspecialchars(trim($_POST['summary_data'] ?? ''));
    $genres       = $_POST['genre_data']        ?? [];
    $imagePath    = NULL; // default: no image

    if (
        $title_data === '' ||
        $author_data === '' ||
        $isbn_data === '' ||
        $summary_data === '' ||
        empty($genres)
    ) {
        $insertMsg = "Please fill in all fields and choose at least one genre.";
    } else {
        
/*
    Be Sure to make the form image be Moved to the book
    images folder after Approval, and Deleted when Denied.
*/
        
        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $insertMsg = "Image upload failed.";
            } elseif ($file['size'] > 4 * 1024 * 1024) {
                $insertMsg = "Image too large (max 4MB).";
            } else {
                $info = new finfo(FILEINFO_MIME_TYPE);
                $mime = $info->file($file['tmp_name']);
                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png'
                ];
                if (!isset($allowed[$mime])) {
                    $insertMsg = "Unsupported image type (use JPG or PNG).";
                } else {
                    $ext   = $allowed[$mime];
                    $bname = basename(random_bytes(16));
                    $fname = $bname . '.' . $ext;

                    $dir  = __DIR__ . '/../../images/forms/';
                    $dest = $dir . DIRECTORY_SEPARATOR . $fname;

                    if (
                        !is_uploaded_file($file['tmp_name']) ||
                        !move_uploaded_file($file['tmp_name'], $dest)
                    ) {
                        $insertMsg = "Failed to store image.";
                    } else {
                        // relative path for use on the site
                        $imagePath = 'forms/' . $fname;
                    }
                }
            }
        }

        if ($insertMsg === "") {
            try {
                $genreString = implode(',', $genres);

                $stmt = $db->prepare(
                    "CALL addForm(
                :title,
                :author,
                :isbn,
                :published,
                :summary,
                :genres,
                :image_path,
                :uid,
                @new_id
            )"
                );

                $published = $publish_data !== '' ? $publish_data : null;

                $stmt->execute([
                    ':title'      => $title_data,
                    ':author'     => $author_data,
                    ':isbn'       => $isbn_data,
                    ':published'  => $published,
                    ':summary'    => $summary_data,
                    ':genres'     => $genreString,
                    ':image_path' => $imagePath,
                    ':uid'        => $userId,
                ]);

                $idRow = $db->query("SELECT @new_id AS form_id")->fetch(PDO::FETCH_ASSOC);

                if (!$idRow || !$idRow['form_id']) {
                    $insertMsg = "Error: request not found.";
                } else {
                    $insertMsg = "Request submitted!";
                }
            } catch (Exception $e) {
                $insertMsg = "Error submitting request: " . $e->getMessage();
            }
        }
    }
}

// ---------- IF ADMIN: LOAD ALL REQUESTS ----------
$allForms = [];
if ($isAdmin) {
    $query = $db->query(
        "SELECT
            f.form_id,
            f.isbn,
            f.title,
            f.author,
            f.published,
            f.summary,
            f.creation_date,
            f.image_path,
            u.username,
            GROUP_CONCAT(fg.genre ORDER BY fg.genre SEPARATOR ', ') AS genres
         FROM forms AS f
         JOIN users AS u ON u.user_id = f.user_id
         LEFT JOIN formgenres AS fg ON fg.form_id = f.form_id
         WHERE f.approve_date IS NULL
         GROUP BY f.form_id
         ORDER BY f.creation_date DESC"
    );
    $allForms = $query->fetchAll();
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book – Book Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="app.css" />
</head>

<body>
    <header>
        <div class="brand">
            <h1>Book</h1>
        </div>
        <nav aria-label="Primary">
            <a class="tab" href="/~bdb/bookworm/search.php">Search</a>
            <a class="tab" href="/~bdb/bookworm/form.php">Book Requests</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-welcome">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a class="tab" href="/~bdb/bookworm/logout.php">Logout</a>
            <?php else: ?>
                <a class="tab" href="/~bdb/bookworm/signin.php">Login / Create</a>
            <?php endif; ?>

            <a class="tab" href="/~bdb/bookworm/top20.php">Top 20 Books</a>
            <a class="tab" href="/~bdb/bookworm/about.php">About</a>
        </nav>
    </header>


    <main>
        <section>
            <h2>Request a book</h2>
            <?php if ($insertMsg !== ""): ?>
                <p class="muted"><?= htmlspecialchars($insertMsg) ?></p>
            <?php endif; ?>

            <form action="form.php" method="POST" enctype="multipart/form-data">
                <label>Title:
                    <input type="text" name="title_data">
                </label><br>

                <label>Author:
                    <input type="text" name="author_data">
                </label><br>

                <label>ISBN:
                    <input type="text" name="isbn_data">
                </label><br>

                <label>Published Date:
                    <input type="date" name="publish_data">
                </label><br>

                <label>Summary:<br>
                    <textarea rows="10" cols="60" name="summary_data"></textarea>
                </label><br>

                <label>Cover image (optional):
                    <input type="file" name="image" accept="image/png,image/jpeg">
                </label><br>

                <label>Genres:</label><br>
                <input type="checkbox" name="genre_data[]" value="Action"> Action<br>
                <input type="checkbox" name="genre_data[]" value="Adventure"> Adventure<br>
                <input type="checkbox" name="genre_data[]" value="Crime"> Crime<br>
                <input type="checkbox" name="genre_data[]" value="Classic"> Classic<br>
                <input type="checkbox" name="genre_data[]" value="Dystopian"> Dystopian<br>
                <input type="checkbox" name="genre_data[]" value="Fantasy"> Fantasy<br>
                <input type="checkbox" name="genre_data[]" value="Historical"> Historical<br>
                <input type="checkbox" name="genre_data[]" value="Horror"> Horror<br>
                <input type="checkbox" name="genre_data[]" value="Non-Fiction"> Non-Fiction<br>
                <input type="checkbox" name="genre_data[]" value="Mystery"> Mystery<br>
                <input type="checkbox" name="genre_data[]" value="Romance"> Romance<br>
                <input type="checkbox" name="genre_data[]" value="Sci-Fi"> Sci-Fi<br>
                <input type="checkbox" name="genre_data[]" value="Thriller"> Thriller<br>

                <input type="submit" name="request_submit" value="Submit Request">
            </form>
        </section>

        <?php if ($isAdmin): ?>
            <section>
                <h2>Pending book requests (admin view)</h2>

                <?php if ($adminMsg !== ""): ?>
                    <p class="muted"><?= htmlspecialchars($adminMsg) ?></p>
                <?php endif; ?>

                <?php if (!$allForms): ?>
                    <p class="muted">No pending requests.</p>
                <?php else: ?>
                    <div class="rows">
                        <?php foreach ($allForms as $f): ?>
                            <div class="row">
                                <div>
                                    <div class="title">
                                        <?= htmlspecialchars($f['title']) ?> (<?= htmlspecialchars($f['isbn']) ?>)
                                    </div>
                                    <div class="author">
                                        by <?= htmlspecialchars($f['author']) ?>
                                    </div>
                                    <div class="muted">
                                        Genres: <?= htmlspecialchars($f['genres'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($f['image_path'])): ?>
                                        <div class="muted">
                                            Image: <?= htmlspecialchars($f['image_path']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="muted">
                                        Requested by: <?= htmlspecialchars($f['username']) ?>
                                    </div>
                                    <div class="muted">
                                        Submitted: <?= htmlspecialchars($f['creation_date']) ?>
                                    </div>

                                    <!-- Approve -->
                                    <form action="form.php" method="POST" style="margin-top:8px;">
                                        <input type="hidden" name="approve_form_id" value="<?= (int)$f['form_id'] ?>">
                                        <button type="submit">Approve</button>
                                    </form>

                                    <!-- Deny -->
                                    <form action="form.php" method="POST" style="margin-top:4px;">
                                        <input type="hidden" name="deny_form_id" value="<?= (int)$f['form_id'] ?>">
                                        <button type="submit">Deny</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

    </main>
</body>

</html>