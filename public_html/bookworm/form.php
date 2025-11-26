<?php
require '/home/stu/bdb/phpTools/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$db       = get_db();
$userId   = (int)$_SESSION['user_id'];

$insertMsg = "";

// ---------- HANDLE NEW REQUEST SUBMIT (users + admins) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_submit'])) {
    $title_data   = trim($_POST['title_data']   ?? '');
    $author_data  = trim($_POST['author_data']  ?? '');
    $isbn_data    = trim($_POST['isbn_data']    ?? '');
    $publish_data = $_POST['publish_data']      ?? '';
    $summary_data = trim($_POST['summary_data'] ?? '');
    $genres       = $_POST['genre_data']        ?? [];
    $imagePath    = null; // default: no image

    if (
        $title_data === '' ||
        $author_data === '' ||
        $isbn_data === '' ||
        $summary_data === '' ||
        empty($genres)
    ) {
        $insertMsg = "Please fill in all fields and choose at least one genre.";
    } else {
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

                    $dir  = '/home/stu/bdb/images/forms/';
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
                    $insertMsg = "Error: request created but ID missing.";
                } else {
                    $insertMsg = "Your request has been submitted!";
                }
            } catch (Exception $e) {
                $insertMsg = "Error submitting request: " . $e->getMessage();
            }
        }
    }
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
    <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>

    <main>
        <section class="request-form-page">
            <div class="request-form__inner">
                <h2>Request a book</h2>

                <?php if ($insertMsg !== ""): ?>
                    <p class="muted"><?= htmlspecialchars($insertMsg) ?></p>
                <?php endif; ?>

                <form
                    action="form.php"
                    method="POST"
                    enctype="multipart/form-data"
                    class="requestForm">
                    <div class="requestForm-grid">
                        <!-- LEFT: main book fields -->
                        <div class="requestForm-left">
                            <label class="requestForm-field">
                                <span class="requestForm-label">Title</span>
                                <input type="text" name="title_data">
                            </label>

                            <label class="requestForm-field">
                                <span class="requestForm-label">Author</span>
                                <input type="text" name="author_data">
                            </label>

                            <label class="requestForm-field">
                                <span class="requestForm-label">ISBN</span>
                                <input type="text" name="isbn_data">
                            </label>

                            <label class="requestForm-field">
                                <span class="requestForm-label">Published Date</span>
                                <input type="date" name="publish_data">
                            </label>

                            <label class="requestForm-field">
                                <span class="requestForm-label">Summary</span>
                                <textarea rows="10" cols="60" name="summary_data"></textarea>
                            </label>

                            <label class="requestForm-field">
                                <span class="requestForm-label">Cover image (optional)</span>
                                <input type="file" name="image" accept="image/png,image/jpeg">
                            </label>
                        </div>

                        <!-- RIGHT: genres box -->
                        <div class="requestForm-right">
                            <div class="form-genres">
                                <div class="form-genres-header">
                                    <span class="requestForm-label">Genres</span>
                                    <span class="form-genres-hint muted">Select all that apply</span>
                                </div>

                                <div class="form-genres-box">
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Action"> Action
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Adventure"> Adventure
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Crime"> Crime
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Classic"> Classic
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Dystopian"> Dystopian
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Fantasy"> Fantasy
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Historical"> Historical
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Horror"> Horror
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Non-Fiction"> Non-Fiction
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Mystery"> Mystery
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Romance"> Romance
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Sci-Fi"> Sci-Fi
                                    </label>
                                    <label class="form-genre-option">
                                        <input type="checkbox" name="genre_data[]" value="Thriller"> Thriller
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="requestForm-actions">
                        <input
                            type="submit"
                            name="request_submit"
                            value="Submit Request"
                            class="requestForm-submit">
                    </div>
                </form>
            </div>
        </section>

    </main>
    <script src="/~bdb/bookworm/search.js"></script>

</body>

</html>