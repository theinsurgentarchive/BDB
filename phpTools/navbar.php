<header>
    <div class="brand">
        <a href="<?=__DIR__ . '/../public_html/bookworm/homepage.php'?>">
            Bookworm
        </a>
    </div> 
    <nav aria-label="Primary">
        <div class="searchArea">\
            <form action="<?= __DIR__ . 'advSearch.php'?>" method="post">
                <input
                    class="tab"
                    id="searchBar"
                    type="text"
                    placeholder="Search..."
                >
                <input
                    type="submit" style="display: none"
                    name="navSearchSubmit" value="navSearchSubmit"
                >
            </form>
            <div id="searchResults"></div>
        </div>

        <?php if (isset($_SESSION['user_id'])):?>
            <span class="nav-welcome">
                Welcome, <?=htmlspecialchars($_SESSION['username'])?>
            </span>
            <a
                class="tab"
                href="<?= __DIR__ . '/../bookworm/logout.php'?>"
            >
                Logout
            </a>
        <?php else:?>
            <a
                class="tab"
                href="<?= __DIR__ . '/../bookworm/signin.php'?>"
            >
                Login / Create
            </a>
        <?php endif;?>

        <a class="tab" href="<?= __DIR__ . '/../bookworm/top20.php'?>">
            Top 20 Books
        </a>
        <a class="tab" href="<?= __DIR__ . '/../bookworm/about.php'?>">
            About
        </a>
    </nav>
</header>
