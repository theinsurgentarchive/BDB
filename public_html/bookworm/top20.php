<?php
require __DIR__ . '/../../phpTools/config.php';

$topTwentyBooks = $pdo->query("SELECT * FROM toptwentybooks")->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book - Top 20</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="app.css">
</head>

<body>
    <?php //require __DIR__ . '/../../phpTools/navbar.php'?>
    <header>
        <div class="brand">
            <h1>Book</h1>
        </div>
        <nav aria-label="Primary">
            <a class="tab" href="/~bdb/bookworm/search.php">Search</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="tab" href="/~bdb/bookworm/form.php">Book Requests</a>
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
            <h2>Top Twenty Books</h2>
            <?php if (!$topTwentyBooks): ?>
                <p class="muted">No book ratings yet.</p>
            <?php else: ?>
                <div class="rows">
                    <?php foreach ($topTwentyBooks as $i => $t): ?>
                        <div class="row">
                            <div>
                                <div class="muted">#<?= $i + 1 ?></div>
                                <div class="title"><?= htmlspecialchars($t['title']) ?></div>
                                <div class="author"><?= htmlspecialchars($t['author']) ?></div>
                            </div>
                            <div>
                                <div>Avg: <?= number_format((float)$t['avg_rating'], 2) ?></div>
                                <div class="muted">Ratings: <?= (int)$t['totalratings'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="<?= __DIR__ . '/../../jsTools/simpleSearch.js'?>"></script>
</body>

</html>