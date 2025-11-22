<?php
require __DIR__ . '/../../phpTools/config.php';
$db = get_db();
$logFile = __DIR__ . '/../logFiles/dynBook.log';

//Insert image path when image found for placeholder:
$altPath = '';

// ---------- HANDLE RATING SUBMIT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        fail(401, 'Login required to rate');
    }

    $userId = (int)$_SESSION['user_id'];
    $bookId = (int)($_POST['bid'] ?? 0);
    $ratingVal = (int)($_POST['rating'] ?? 0);

    if ($bookId <= 0) {
        fail(400, 'Book ID Required');
    }
    if ($ratingVal < 1 || $ratingVal > 5) {
        fail(400, 'Rating must be 1–5');
    }

    // one row per (user, book) 
    $stmt = $db->prepare("
        INSERT INTO ratings (book_id, user_id, rating)
        VALUES (:bid, :uid, :rating)
        ON DUPLICATE KEY UPDATE
            rating = VALUES(rating),
            creation_date = CURRENT_TIMESTAMP
    ");
    $stmt->execute([
        ':bid'    => $bookId,
        ':uid'    => $userId,
        ':rating' => $ratingVal
    ]);

    header("Location: dynBook.php?bid=" . $bookId . "&rated=1");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fail(405, 'Method Not Allowed');
}

if (empty($_GET['bid'])) {
    fail(400, 'Book ID Required');
}
$book_id = htmlspecialchars($_GET['bid']);

$stmt = $db->prepare('SELECT * FROM books WHERE book_id = ?');
$stmt->bindParam(1, $book_id, PDO::PARAM_INT);
$stmt->execute();

$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) {
    fail(500, 'Book Not Found');
}

$stmt = $db->prepare(
    'SELECT genre FROM bookgenres WHERE book_id = ?'
);
$stmt->bindParam(1, $book_id, PDO::PARAM_INT);
$stmt->execute();

$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$genres) {
    fail(500, ("Cannot Find Genres for Book_ID: " . $book_id));
}

// ---------- RATING DATA ----------
$avgStmt = $db->prepare("
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings
    FROM ratings
    WHERE book_id = ?
");
$avgStmt->bindParam(1, $book_id, PDO::PARAM_INT);
$avgStmt->execute();
$ratingStats = $avgStmt->fetch(PDO::FETCH_ASSOC);

$userRating = null;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];

    $uStmt = $db->prepare("
        SELECT rating
        FROM ratings
        WHERE book_id = ? AND user_id = ?
    ");
    $uStmt->execute([$book_id, $uid]);
    $row = $uStmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $userRating = (int)$row['rating'];
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book - <?= $book['title'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="app.css">
</head>

<body>
    <header>
        <div class="brand">
            <!-- add your icon file path here -->
            <img class="brand-icon" src="/~bdb/bookworm/images/site-icon.png" alt="Site icon">
            <h1>BookWorm</h1>
        </div>

        <nav aria-label="Primary">

            <form class="nav-search" action="/~bdb/bookworm/search.php" method="GET">
                <input
                    type="text"
                    name="q"
                    placeholder="Search books..."
                    aria-label="Search books">
            </form>


            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="tab" href="/~bdb/bookworm/adminTool.php">Admin Tool</a>
                <?php endif; ?>

                <a class="tab" href="/~bdb/bookworm/form.php">Book Requests</a>

                <a class="tab" href="/~bdb/bookworm/logout.php">Logout</a>

                <span class="nav-welcome">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>

            <?php else: ?>

                <a class="tab" href="/~bdb/bookworm/signin.php">Login / Create</a>

            <?php endif; ?>

            <a class="tab" href="#">Advance Search</a>

            <a class="tab" href="/~bdb/bookworm/top20.php">Top 20 Books</a>
            <a class="tab" href="/~bdb/bookworm/about.php">About</a>
        </nav>
    </header>

    <main>
        <section>
            <div class="bookContainer">
                <img src="<?= $book['image_path'] ?>" alt="<?= $altPath ?>">
                <h1><?= $book['title'] ?></h1>
                <div class="bookInfo">
                    <p class="bookISBN"><?= $book['isbn'] ?></p>
                    <p class="bookAuthor"><?= $book['author'] ?></p>
                    <span
                        class="bookPublish"
                        data-date="<?= htmlspecialchars($book['published']) ?>">
                        <?= htmlspecialchars($book['published']) ?>
                    </span>
                    <div class="grid">
                        <?php foreach ($genres as $g): ?>
                            <a href="<?= "/~bdb/bookworm/search.php/?genres=" . $g['genre'] ?>">
                                <?= $g['genre'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <!-- ---------- Rating Display + Form ---------- -->
                    <div class="ratingBlock">
                        <?php
                        $avgRating = $ratingStats['avg_rating'] ? round((float)$ratingStats['avg_rating'], 1) : 0;
                        $totalRatings = (int)$ratingStats['total_ratings'];
                        ?>
                        <p class="muted">
                            Avg rating: <?= $avgRating ?> / 5 (<?= $totalRatings ?> ratings)
                        </p>

                        <?php if (!empty($_GET['rated'])): ?>
                            <div id="ratingToast" class="ratingToast">
                                Rating submitted!
                            </div>
                        <?php endif; ?>


                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" class="starRatingForm">
                                <input type="hidden" name="bid" value="<?= (int)$book_id ?>">

                                <div class="starRating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input
                                            type="radio"
                                            id="star<?= $i ?>"
                                            name="rating"
                                            value="<?= $i ?>"
                                            <?= ($userRating === $i) ? 'checked' : '' ?>
                                            required>
                                        <label for="star<?= $i ?>">★</label>
                                    <?php endfor; ?>
                                </div>

                                <button type="submit">
                                    <?= ($userRating !== null) ? "Update Rating" : "Submit Rating" ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="muted">Log in to rate this book.</p>
                        <?php endif; ?>
                    </div>

                    <p class="bookSummary"><?= $book['summary'] ?></p>
                </div>
            </div>
            <div class="commentContainer">
                <!--Generate Comments After Form!-->
                <form></form>

                <?php /*foreach ($comments as $c):?>
                <?php endforeach; */ ?>
            </div>

        </section>
    </main>
    <script>
        // Find all date spans
        document.querySelectorAll('.bookPublish').forEach(span => {
            const raw = span.dataset.date;
            const date = new Date(raw);

            //Format to the user's locale
            const formatted = new Intl.DateTimeFormat(navigator.language, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);

            span.textContent = formatted;
        });
        const ratingToast = document.getElementById('ratingToast');
        if (ratingToast) {
            setTimeout(() => {
                ratingToast.classList.add('hide');
            }, 1500);
        }

        const starForm = document.querySelector('.starRatingForm');
        if (starForm) {
            starForm.addEventListener('submit', () => {
                const btn = starForm.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = "Submitting...";
                }
            });
        }
    </script>
</body>

</html>