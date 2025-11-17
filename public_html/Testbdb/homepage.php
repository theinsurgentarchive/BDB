<?php
require '/home/stu/bdb/phpTools/config.php';
$db = get_db();

$randomBooks   = $db->query("SELECT * FROM randbooks")->fetchAll();
$topGenres     = $db->query("SELECT * FROM randbookgenres")->fetchAll();
$topThreeBooks = $db->query("SELECT * FROM topthreebooks")->fetchAll();
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
    <header>
        <div class="brand">
            <h1>Book</h1>
        </div>
        <nav aria-label="Primary">
            <a class="tab" href="/~bdb/Testbdd/search.php">Search</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-welcome">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a class="tab" href="/~bdb/Testbdd/logout.php">Logout</a>
            <?php else: ?>
                <a class="tab" href="/~bdb/Testbdd/signin.php">Login / Create</a>
            <?php endif; ?>

            <a class="tab" href="/~bdb/Testbdd/top20.php">Top 20 Books</a>
            <a class="tab" href="/~bdb/Testbdd/about.php">About</a>
        </nav>

    </header>

    <main>

        <section>
            <h2>Random books</h2>
            <?php if (!$randomBooks): ?>
                <p class="muted">No books to show yet.</p>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($randomBooks as $b): ?>
                        <div class="card">
                            <img src="<?= htmlspecialchars($b['image_path'] ?? '') ?>" alt="">
                            <div class="title"><?= htmlspecialchars($b['title']) ?></div>
                            <div class="author"><?= htmlspecialchars($b['author']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <h2>Top genres (avg rating)</h2>
            <?php if (!$topGenres): ?>
                <p class="muted">No genre ratings yet.</p>
            <?php else: ?>
                <div class="rows">
                    <?php foreach ($topGenres as $g): ?>
                        <div class="row">
                            <div><?= htmlspecialchars($g['genre']) ?></div>
                            <div><?= number_format((float)$g['avg_rating'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <h2>Top three books</h2>
            <?php if (!$topThreeBooks): ?>
                <p class="muted">No book ratings yet.</p>
            <?php else: ?>
                <div class="rows">
                    <?php foreach ($topThreeBooks as $t): ?>
                        <div class="row">
                            <div>
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
</body>

</html>