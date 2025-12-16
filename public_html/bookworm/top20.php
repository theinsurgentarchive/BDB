<?php
require '/home/stu/bdb/phpTools/config.php';

$topTwentyBooks = $pdo->query("SELECT * FROM toptwentybooks")->fetchAll();
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Book - Top 20</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/~bdb/bookworm/app.css">
    </head>

    <body>
        <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>

        <main>
            <section class="top20-page">
                <div class="top20-page__inner">
                    <h2>Top Twenty Books</h2>

                    <?php if (!$topTwentyBooks): ?>
    <p class="muted">No book ratings yet.</p>
<?php else: ?>

    <div class="top20-actions">
        <button
            type="button"
            class="leader-print"
            onclick="window.print();"
        >
            Print Top 20
        </button>
    </div>

    <div class="rows top20-list">
        <?php foreach ($topTwentyBooks as $i => $t): ?>
            <a class="cardLink top20-item"
               href="<?="/~bdb/bookworm/dynBook.php/?bid=" . htmlspecialchars($t['book_id'] ?? '')?>">
                <div class="row top20-row">
                    <div class="top20-left">
                        <div class="top20-rank">
                            <?= $i + 1 ?>
                        </div>
        
                        <img
                            src="<?= htmlspecialchars($t['image_path'] ?? $altPath) ?>"
                            alt=""
                            class="top20-img">
        
                        <div class="top20-meta">
                            <div class="title"><?= htmlspecialchars($t['title']) ?></div>
                            <div class="author"><?= htmlspecialchars($t['author']) ?></div>
                        </div>
                    </div>
        
                    <div class="top20-right">
                        <div class="top20-rating">
                            <span class="top20-rating-star">★</span>
                            <span>Avg: <?= number_format((float)$t['avg_rating'], 2) ?></span>
                        </div>
                        <div class="muted">
                            Ratings: <?= (int)$t['totalratings'] ?>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

                </div>
            </section>
        </main>
    </body>
</html>