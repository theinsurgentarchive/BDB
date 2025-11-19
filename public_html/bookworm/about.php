<?php
require __DIR__ . '/../../phpTools/config.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book – About</title>
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
            <a class="tab" href="/~bdb/bookworm/signin.php">Login / Create</a>
            <a class="tab" href="/~bdb/bookworm/top20.php">Top 20 Books</a>
            <a class="tab" href="/~bdb/bookworm/about.php" aria-current="page">About</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>About Book</h2>
            <p><strong>Book</strong> is a simple, student-built book rating and discovery website.
                It highlights great reads, shows trending genres, and ranks the top titles by reader ratings.</p>

            <h3>How it works</h3>
            <ul>
                <li><strong>Homepage:</strong> shows 10 random books, top genres by average rating, and the top 3 books overall.</li>
                <li><strong>Top 20:</strong> lists the twenty highest-rated books across the database.</li>
                <li><strong>Database views:</strong> powered by <code>randbooks</code>, <code>randbookgenres</code>, <code>topthreebooks</code>, and <code>toptwentybooks</code> views inside <code>bdb</code>.</li>
                <li><strong>Stack:</strong> PHP + PDO + MariaDB on Artemis (Apache).</li>
                <li><strong>Security:</strong> uses parameterized queries, UTF-8 encoding, and HTML escaping to prevent injection or XSS.</li>
            </ul>
        </section>

        <section>
            <h2>Moderation Rules</h2>
            <ol>
                <li><strong>Be civil.</strong> No harassment, slurs, or threats.</li>
                <li><strong>Stay on topic.</strong> Discuss the book or author only.</li>
                <li><strong>No spoilers without warning.</strong> Mark spoilers clearly.</li>
                <li><strong>No hate speech or discrimination.</strong></li>
                <li><strong>No NSFW or graphic content.</strong></li>
                <li><strong>No piracy or illegal requests.</strong></li>
                <li><strong>No spam or excessive self-promotion.</strong></li>
                <li><strong>Be honest.</strong> No fake or paid reviews.</li>
                <li><strong>Respect privacy.</strong> Don’t share personal information.</li>
                <li><strong>Follow moderator directions.</strong> Ignoring them can lead to suspension.</li>
            </ol>
            <p><em>Moderation process:</em> warnings → content removal → suspension → permanent ban if violations persist.
                Appeals can be made through the site’s contact email; moderators provide a brief rationale and final decision.</p>
        </section>

        <section>
            <h2>Credits</h2>
            <p>Created by students at California State University, Bakersfield as part of a database and web development project.</p>
        </section>
    </main>
    <script src="<?= __DIR__ . '/../../jsTools/simpleSearch.js'?>"></script>
</body>
</html>