<header>
    <div class="brand">
        <a href="/~bdb/bookworm/homepage.php">
            Bookworm
        </a>
    </div> 
    <nav aria-label="Primary">
        <div class="searchArea">
            <form action="/~bdb/bookworm/advsearch.php" method="get">
                <input
                    class="tab"
                    name="query"
                    id="searchBar"
                    type="text"
                    placeholder="Search..."
                >
                <input type="submit" style="display: none">
            </form>
            <div id="searchResults"></div>
        </div>

        <?php if (isset($_SESSION['user_id'])):?>
            <span class="nav-welcome">
                Welcome, <?=htmlspecialchars($_SESSION['username'])?>
            </span>
            <a
                class="tab"
                href="/~bdb/bookworm/logout.php"
            >
                Logout
            </a>
        <?php else:?>
            <a
                class="tab"
                href="/~bdb/bookworm/signin.php"
            >
                Login / Create
            </a>
        <?php endif;?>

        <a class="tab" href="/~bdb/bookworm/top20.php">
            Top 20 Books
        </a>
        <a class="tab" href="/~bdb/bookworm/about.php">
            About
        </a>
    </nav>
</header>
