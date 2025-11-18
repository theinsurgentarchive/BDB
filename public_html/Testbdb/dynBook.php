<?php
    require __DIR__ . '../../phpTools/config.php';
    $db = get_db();
    $logFile = __DIR__ . '/../logFiles/dynBook.log';

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
                            <a href="<?="/~/bdb/Testbdd/search.php/?genres=" . $g['genre']?>">
                                <?=$g['genre']?>
                            </a>
                        <?php endforeach;?>
                    </div>
                    <p class="bookSummary"><?=$book['summary']?></p>
                </div>
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
  </script>
</body>
</html>