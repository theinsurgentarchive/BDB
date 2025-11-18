<header>
    <div class="brand">
        <h1>Book</h1>
    </div>
    <nav aria-label="Primary">
        <div class="searchArea">\
            <input
                class="tab"
                id="searchBar"
                type="text"
                placeholder="Search..."
            >
            <div id="searchResults"></div>
        </div>

        <?php if (isset($_SESSION['user_id'])):?>
            <span class="nav-welcome">
                Welcome, <?=htmlspecialchars($_SESSION['username'])?>
            </span>
            <a class="tab" href="/~bdb/Testbdd/logout.php">Logout</a>
        <?php else:?>
            <a class="tab" href="/~bdb/Testbdd/signin.php">Login / Create</a>
        <?php endif;?>

        <a class="tab" href="/~bdb/Testbdd/top20.php">Top 20 Books</a>
        <a class="tab" href="/~bdb/Testbdd/about.php">About</a>
    </nav>
</header>
