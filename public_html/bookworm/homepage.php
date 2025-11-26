<?php
require_once __DIR__ . '/../../phpTools/config.php';
$db = get_db();

$randomBooks   = $db->query("SELECT * FROM randbooks")->fetchAll();
$topGenres     = $db->query("SELECT * FROM randbookgenres")->fetchAll();
$topFiveBooks = $db->query("SELECT * FROM topfivebooks")->fetchAll();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="app.css">
</head>

<body>
    <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>
    <section class="homepage-box">
        <main class="homepage-layout">

            <!-- LEFT: random books grid -->
            <div class="homepage-layout__left">
                <section class="homepage-random-books">
                    <div class="homepage-random-books__inner">
                        <h2>Check these books out</h2>

                        <?php if (!$randomBooks): ?>
                            <p class="muted">No books to show yet.</p>
                        <?php else: ?>
                            <div class="homepage-random-books__grid">
                                <?php foreach ($randomBooks as $b): ?>
                                    <a class="cardLink" href="<?= "/~bdb/bookworm/dynBook.php/?bid=" . htmlspecialchars($b['book_id'] ?? '') ?>">
                                        <div class="card">
                                            <img src="<?= htmlspecialchars($b['image_path'] ?? '') ?>" alt="">
                                            <div class="title"><?= htmlspecialchars($b['title']) ?></div>
                                            <div class="author"><?= htmlspecialchars($b['author']) ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- RIGHT: stacked sidebar (top genres + top three books) -->
            <aside class="homepage-layout__right">

                <section class="homepage-top-genres">
                    <h2>Top genres</h2>
                    <?php if (!$topGenres): ?>
                        <p class="muted">No genre ratings yet.</p>
                    <?php else: ?>
                        <div class="rows">
                            <?php foreach ($topGenres as $g): ?>
                                <a class="cardLink" href="<?= "/~bdb/bookworm/advSearch.php/?genres[]=" . htmlspecialchars($g['genre'] ?? '') ?>">
                                    <div class="row">
                                        <div><?= htmlspecialchars($g['genre']) ?></div>
                                        <div><?= number_format((float)$g['avg_rating'], 2) ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="homepage-top-books">
                    <h2>Top Five Books</h2>
                    <?php if (!$topFiveBooks): ?>
                        <p class="muted">No book ratings yet.</p>
                    <?php else: ?>
                        <div class="rows">
                            <?php foreach ($topFiveBooks as $t): ?>
                                <a class="homepage-top-books__link"
                                    href="<?= "/~bdb/bookworm/dynBook.php/?bid=" . htmlspecialchars($t['book_id'] ?? '') ?>">
                                    <div class="row">
                                        <div class="homepage-top-books__left">
                                            <img
                                                src="<?= htmlspecialchars($t['image_path'] ?? '') ?>"
                                                alt=""
                                                class="homepage-top-books__img">
                                            <div>
                                                <div class="title"><?= htmlspecialchars($t['title']) ?></div>
                                                <div class="author"><?= htmlspecialchars($t['author']) ?></div>
                                            </div>
                                        </div>
                                        <div class="homepage-top-books__stats">
                                            <div>Avg: <?= number_format((float)$t['avg_rating'], 2) ?></div>
                                            <div class="muted">Ratings: <?= (int)$t['totalratings'] ?></div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            </aside>
        </main>
    </section>

    <script src="/~bdb/bookworm/search.js"></script>
</body>

</html>