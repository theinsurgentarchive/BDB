<?php
    require_once __DIR__ . '/../../phpTools/config.php';
    $db = get_db();

    //Insert image path when image found for placeholder:
    $altPath = '';

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
    <title>Book - <?=$book['title']?></title>
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

            <?php if (isset($_SESSION['user_id'])):?>
                <span class="nav-welcome">
                    Welcome, <?=htmlspecialchars($_SESSION['username'])?>
                </span>
                <a class="tab" href="/~bdb/bookworm/logout.php">Logout</a>
            <?php else:?>
                <a class="tab" href="/~bdb/bookworm/signin.php">Login / Create</a>
            <?php endif;?>

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
                    <!--Javier Rating Code Below!-->
                    <p class="bookRating"><?=$rating['rating']?></p>
                    <p class="bookAuthor"><?=$book['author']?></p>
                    <span
                        class="bookPublish"
                        data-date="<?=htmlspecialchars($book['published'])?>"
                    >
                        <?=htmlspecialchars($book['published'])?>
                    </span>
                    <div class="grid">
                        <?php foreach ($genres as $g):?>
                            <a href="<?="/~/bdb/bookworm/advSearch.php/?genres=" . $g['genre']?>">
                                <?=$g['genre']?>
                            </a>
                        <?php endforeach;?>
                    </div>
                    <p class="bookSummary"><?=$book['summary']?></p>
                </div>
            </div>
        </section>

        <section>
            <div class="commentContainer">
                <?php if (isset($_SESSION['user_id'])):?>
                    <form action="<?= __DIR__ . '/../../phpTools/addComment.php'?>" method="post">
                        <input type="hidden" value="<?=$_SESSION['user_id']?>">
                        <textarea
                            name="comment_text" rows="10" cols="60"
                            placeholder="Write Comment Here"
                        ></textarea>
                        <input
                            type="submit" name="commentSubmit"
                            value="commentSubmit"
                        >
                    </form>
                    <?php foreach ($comments as $c):?>
                        <div class="commentCard">
                            <img src="">
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
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
  <script src="<?= __DIR__ . '/../../jsTools/simpleSearch.js'?>"></script>
</body>
</html>