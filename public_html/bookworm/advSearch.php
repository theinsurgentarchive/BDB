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
    $results .= "<img class='book-card-img' src='"
        . htmlspecialchars($row['image_path'] ?? '')
        . "' alt='" . htmlspecialchars($row['title']) . "'>";
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
    <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>

    <main>
        <section class="advSearch-page">
            <div class="advSearch__inner">
                <h2>Advanced Search</h2>

                <div class="advSearch-hint muted">
                    Search by title, author, and one or more genres.
                </div>

                <form
                    action="<?= $_SERVER['PHP_SELF'] ?>"
                    method="GET"
                    class="advSearch-form">
                    <!-- Book / title keywords -->
                    <label class="advSearch-field">
                        <span class="advSearch-label">Book title / keywords</span>
                        <input
                            type="text"
                            name="query"
                            placeholder="Search by title or keywords..."
                            value="<?= htmlspecialchars($query) ?>">
                    </label>

                    <!-- Author -->
                    <label class="advSearch-field">
                        <span class="advSearch-label">Author</span>
                        <input
                            type="text"
                            name="author"
                            placeholder="Author name..."
                            value="<?= htmlspecialchars($author) ?>">
                    </label>

                    <!-- Genres -->
                    <div class="advSearch-field">
                        <div class="advSearch-labelRow">
                            <span class="advSearch-label">Genres</span>
                            <span class="advSearch-labelHint muted">Select all that apply</span>
                        </div>

                        <div class="form-genres-box advSearch-genres-box">
                            <?php foreach ($gs as $g): ?>
                                <?php
                                $checked = in_array($g['genre'], (array)$genresRaw, true) ? 'checked' : '';
                                ?>
                                <label class="form-genre-option">
                                    <input
                                        type="checkbox"
                                        name="genres[]"
                                        value="<?= htmlspecialchars($g['genre']) ?>"
                                        <?= $checked ?>>
                                    <?= htmlspecialchars($g['genre']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="advSearch-actions">
                        <input type="submit" value="Search" class="advSearch-submit">
                    </div>
                </form>
            </div>
        </section>


        <section class="advSearch-results">
            <div class="advSearch-results__inner">
                <h2>Search Results</h2>

                <div id="searchResults" class="advSearch-results-list">
                    <?php
                    if ($results !== '') {
                        echo $results;
                    } elseif (!empty($query) || !empty($author) || !empty($genres)) {
                        echo "<p class='muted'>No results found.</p>";
                    } else {
                        echo "<p class='muted'>Begin searching for books to see results.</p>";
                    }
                    ?>
                </div>
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
</body>

</html>