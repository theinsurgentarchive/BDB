<?php
    require __DIR__ . '/../../phpTools/config.php';
    $db = get_db();

    $user_id = $_SESSION['user_id'] ?? '';

    // ---------- HANDLE RATING SUBMIT ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($user_id)) {
            fail(401, 'Login required to rate');
        }

        $userId = (int)$user_id;
        $bookId = (int)($_POST['bid'] ?? '');
        $ratingVal = (int)($_POST['rating'] ?? 0);

        if (empty($bookId)) {
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
    $stmt->bindParam(1,$book_id,PDO::PARAM_INT);
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

    $stmt = $db->prepare('SELECT * FROM usercomments WHERE book_id = ?');
    $stmt->bindParam(1,$book_id,PDO::PARAM_INT);
    $stmt->execute();

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $comm = '';
    foreach ($comments as $c) {
        $temp = "<div class='comment-card' id='" . $c['comment_id'] . "'>";
        $temp .= "<img class='commentPic' src='" . $c['image_path'] . "' alt='" . $altPath . "'>";  
        $temp .= "<h4 class='commentUser'>" . $c['username'] . "</h4>";
        $temp .= "<label><b>Creation Date: </b></label>";
        $temp .= "<span class='commentDate' data-date='" . htmlspecialchars($c['creation_date']) . "'>" . $c['creation_date'] . "</span>";
        if ($c["user_id"] === $user_id) {
            $temp .= "<script>console.log('Comment " . $c['comment_id'] . " Belongs to Session User.');</script>";
            $temp .= "<p style='white-space: pre-wrap'>" . $c['comment_text'] . "</p>";
        } else {
            $temp .= "<p style='white-space: pre-wrap'>" . $c['comment_text'] . "</p>";
        }
        if (!empty($c["modified_date"])) {
            $temp .= "<label><b>Edited On: </b></label>";
            $temp .= "<span class='commentDate' data-date='" . htmlspecialchars($c['modified_date']) . "'>" . $c['modified_date'] . "</span>";
        }
        if ($c["depth"] < 6 && !empty($user_id)) {
            $temp .= "<button class='replyButton' data-id='" . $c['comment_id'] . "'>Reply</button>";
            $temp .= "<form class='replyForm hidden' data-id='" . $c['comment_id'] . "'>";
            $temp .= "<input type='hidden' name='bid' value='$book_id'>";
            $temp .= "<input type='hidden' name='uid' value='$user_id'>";
            $temp .= "<input type='hidden' name='pid' value='" . $c["comment_id"] . "'>";
            $temp .= "<textarea rows='5' cols='50' name='commentText' placeholder='Write a reply...'></textarea><br>";
            $temp .= "<input type='submit'>";
            $temp .= "<button class='replyCancel' data-id='" . $c['comment_id'] . "'>Cancel</button>";
            $temp .= "</form>";
        }
        $temp .= "</div>";
        $comm .= $temp;
    }

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Book - <?=$book['title']?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/~bdb/bookworm/app.css">
</head>

<body>
    <?php //require __DIR__ . '/../../phpTools/navbar.php'?>
    <header>
        <div class="brand">
            <!-- add your icon file path here -->
            <img class="brand-icon" src="/~bdb/bookworm/images/site-icon.png" alt="Site icon">
            <h1>BookWorm</h1>
        </div>

        <nav aria-label="Primary">

            <form class="nav-search" action="/~bdb/bookworm/advSearch.php" method="GET">
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
                <img src="<?=$book['image_path']?>" alt="<?=$altPath?>">
                <h1><?=$book['title']?></h1>
                <div class="bookInfo">    
                    <p class="bookISBN"><?=$book['isbn']?></p>
                    <p class="bookAuthor"><?=$book['author']?></p>
                    <span
                        class="bookPublish"
                        data-date="<?=htmlspecialchars($book['published'])?>"
                    >
                        <?=htmlspecialchars($book['published'])?>
                    </span>
                    <div class="grid">
                        <?php foreach ($genres as $g):?>
                            <a href="<?="/~bdb/bookworm/advSearch.php?genres[]=" . urlencode($g['genre'])?>">
                                <?=$g['genre']?>
                            </a>
                        <?php endforeach;?>
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
                    
                    <p class="bookSummary" style='white-space: pre-wrap'><?=$book['summary']?></p>
                </div>
            </div>
        </section>

        <section>
            <div class="commentContainer">
                <!--Generate Comments After Form!-->
                <?php if (!empty($user_id)): ?>
                    <form class="commentForm" method="POST">
                        <label for="commentText"><h4>Enter Comment Here:</h4></label><br>
                        <textarea rows="5" cols="50" name="commentText" placeholder="Comment Here..."></textarea>
                        <input type="hidden" name="bid" value="<?= $book_id?>">
                        <input type="hidden" name="uid" value="<?= $_SESSION['user_id']?>"><br>
                        <input type="submit">
                    </form>
                <?php else: ?>
                    <h4>Sign-in/Register to Comment:</h4>
                <?php endif;?>
                <br>
                <div id="commentSection">
                    <?php echo $comm;?>
                </div>
            </div>
        </section>
    </main>
    <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.body.addEventListener('click', event => {
                    if (event.target.classList.contains('replyButton')) {
                        const id = event.target.dataset.id;
                        const form = document.querySelector(`.replyForm[data-id="${id}"]`);
                        const rb = event.target;
                        
                        if (form) {
                          form.classList.remove('hidden');
                          rb.classList.add('hidden');
                        }
                    }
                    if (event.target.classList.contains('replyCancel')) {
                      const id = event.target.dataset.id;
                      const form = document.querySelector(`.replyForm[data-id="${id}"]`);
                      const rb = document.querySelector(`.replyButton[data-id="${id}"]`);
                    
                      if (form && rb) {
                        form.classList.add('hidden');
                        rb.classList.remove('hidden');
                        form.reset();
                      }
                    }
                });
            });
            document.querySelectorAll('.commentForm').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    fetch('/~bdb/bookworm/addComment.php', {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.json()).then(result => {
                        console.log('Form submitted successfully! ' + JSON.stringify(result));
                        // Optionally, clear the form or provide other feedback
                        form.reset();
                    }).catch(error => {
                        console.error('Error:', error);
                    });
                }); 
            });
            document.querySelectorAll('.replyForm').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    fetch('/~bdb/bookworm/addComment.php', {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.json()).then(result => {
                        console.log('Form submitted successfully! ' + JSON.stringify(result));
                        // Optionally, clear the form or provide other feedback
                        form.reset();
                    }).catch(error => {
                        console.error('Error:', error);
                    });
                }); 
            });
        // Find all date spans
        document.querySelectorAll('.bookPublish').forEach(span => {
            const raw = span.dataset.date;
            const date = new Date(raw);
            
            //Format to the user's locale
            const formatted = new Intl.DateTimeFormat(navigator.language, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }).format(date);

            span.textContent = formatted;
        });
        document.querySelectorAll('.commentDate').forEach(span => {
            const raw = span.dataset.date;
            const date = new Date(raw);
            
            //Format to the user's locale
            const formatted = new Intl.DateTimeFormat(navigator.language, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: "numeric",
                minute: "numeric"
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