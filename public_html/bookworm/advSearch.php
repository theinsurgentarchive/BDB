<?php
require_once __DIR__ . '/../../phpTools/config.php';
$db = get_db();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fail(405, 'Method Not Allowed');
}

$query = htmlspecialchars(trim($_GET['query'] ?? ''));
$author = htmlspecialchars(trim($_GET['author'] ?? ''));
$genresRaw = $_GET['genres'] ?? [];
$genres = !empty($genresRaw) ? implode(',', (array)$genresRaw) : null;
try {
    $stmtGenres = $db->query('SELECT * FROM genres');
    $gs = $stmtGenres->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<pre>SQL Error: ' . htmlspecialchars($e->getMessage()) . '</pre>');
}

$rows = [];
if (!empty($query) || !empty($author) || !empty($genres)) {
    try {
        $stmt = $db->prepare('CALL advSearch(?, ?, ?)');
        if (!empty($query)) {
            $stmt->bindParam(1, $query, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(1, null, PDO::PARAM_NULL);
        }
        if (!empty($genres)) {
            $stmt->bindParam(2, $genres, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(2, null, PDO::PARAM_NULL);
        }
        if (!empty($author)) {
            $stmt->bindParam(3, $author, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(3, null, PDO::PARAM_NULL);
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        while ($stmt->nextRowset()) {
        }
        $stmt->closeCursor();
    } catch (PDOException $e) {
        die('<pre>SQL Error (advSearch): ' . htmlspecialchars($e->getMessage()) . '</pre>');
    }
}

$results = '';
foreach ($rows as $row) {
    $results .= "<div class='book-card' id='" . $row['book_id'] . "'>";
    $results .= "<a href='/~bdb/bookworm/dynBook.php?bid=" . $row['book_id'] . "' class='book-link'>";
    $results .= "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
    $results .= "<h4>" . htmlspecialchars($row["author"]) . "</h4>";
    $results .= "<span class='bookPublish' data-date=\"" . htmlspecialchars($row['published']) . "\">" . htmlspecialchars($row['published']) . "</span>";
    $results .= "<div class='resultGenres grid'>";
    $res = explode(',', $row['genres']);
    foreach ($res as $g) {
        $g = trim($g);
        $url = "/~bdb/bookworm/advSearch.php?genres[]=" . urlencode($g);
        $results .= '<a class="searchGenre" href="' . $url . '">' . htmlspecialchars($g) . '</a>';
    }
    $results .= "</div>";
    $results .= "</a>";
    $results .= "<p style='white-space: pre-wrap'>" . htmlspecialchars($row["summary"]) . "</p>";
    $results .= "</div>";
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Bookworm - Advanced Search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/~bdb/bookworm/app.css">
</head>

<body>
    <?php //require __DIR__ . '/../../phpTools/navbar.php' 
    ?>
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
            <div class="searchForm">
                <h2>Advanced Search</h2>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
                    <input type="text" name="query" placeholder="Search Here..." value="<?= htmlspecialchars($query) ?>"><br>
                    <label for="genres[]"><b>Search by Genre (Inclusive):</b></label>
                    <div class="grid">
                        <?php
                        foreach ($gs as $g) {
                            $checked = in_array($g['genre'], (array)$genresRaw, true) ? 'checked' : '';
                            echo "<label><input type='checkbox' name='genres[]' value='" . htmlspecialchars($g['genre']) . "' $checked> " . htmlspecialchars($g['genre']) . "</label>";
                        }
                        ?>
                    </div>
                    <label for="author">Search by Author:</label>
                    <input type="text" name="author" placeholder="Author Name Here..." value="<?= htmlspecialchars($author) ?>">
                    <br>
                    <input type="submit">
                </form>
            </div>
        </section>

        <section>
            <h2>Search Results:</h2>
            <div id="searchResults">
                <?php
                if ($results !== '') {
                    echo $results;
                } elseif (!empty($query) || !empty($author) || !empty($genres)) {
                    echo "<p>No results found.</p>";
                } else {
                    echo "<p>Begin searching for books to see results.</p>";
                }
                ?>
            </div>
        </section>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Listen for clicks on any .book-card
                document.querySelectorAll('.book-card').forEach(card => {
                    card.addEventListener('click', e => {
                        // If the click happened on a link (<a>), let it behave normally
                        if (e.target.tagName.toLowerCase() === 'a') return;
                        // Otherwise, go to dynBook.php with the book_id
                        const bookId = card.dataset.bookid;
                        window.location.href = '/~bdb/bookworm/dynBook.php?book_id=${encodeURIComponent(bookId)}';
                    });
                });
            });
            window.addEventListener('load', () => {
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
            });
        </script>
    </main>
    <script src="/~bdb/bookworm/search.js"></script>

</body>

</html>