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