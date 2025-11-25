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
    <link rel="stylesheet" href="app.css">
</head>

<body>
    <header>
        <div class="brand">
            <a href="/~bdb/bookworm/homepage.php" class="brand-link">
                <img class="brand-icon" src="/~bdb/images/asset/site-icon.png" alt="Site icon">
                <h1>BookWorm</h1>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-welcome">
                    Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
            <?php endif; ?>
        </div>


        <nav aria-label="Primary">

            <form class="nav-search live-search-container"
                action="/~bdb/bookworm/advSearch.php"
                method="GET">

                <input
                    type="text"
                    id="liveSearch"
                    name="query"
                    placeholder="Search books..."
                    autocomplete="off"
                    onkeyup="searchpartial(event)"
                    aria-label="Search books">

                <div id="results" class="live-results"></div>
            </form>

            <a class="tab" href="/~bdb/bookworm/top20.php">Top 20 Books</a>
            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="tab" href="/~bdb/bookworm/adminTool.php">Admin Tool</a>
                <?php endif; ?>

                <a class="tab" href="/~bdb/bookworm/form.php">Book Requests</a>
                <a class="tab" href="/~bdb/bookworm/profile.php">Profile</a>
                <a class="tab" href="/~bdb/bookworm/logout.php">Logout</a>

            <?php else: ?>

                <a class="tab" href="/~bdb/bookworm/signin.php">Login / Create</a>

            <?php endif; ?>
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
    <script src="/~bdb/bookworm/search.js"></script>

</body>

</html>