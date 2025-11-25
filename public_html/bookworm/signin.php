<?php
require_once __DIR__ . '/../../phpTools/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book – Login / Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="app.css" />
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

    <main class="auth-main">
        <div class="auth-container">

            <form id="loginForm" class="auth-form">
                <h2>Sign In</h2>
                <input type="text" name="username" placeholder="Username" required minlength="6" maxlength="32" />
                <input type="password" name="password" placeholder="Password" required minlength="6" maxlength="20" />
                <button type="submit">Login</button>
                <div class="msg" id="loginMsg"></div>
            </form>

            <form id="registerForm" enctype="multipart/form-data" class="auth-form hidden">
                <h2>Create Account</h2>
                <input type="text" name="username" placeholder="Username (6-32 chars)" required minlength="6" maxlength="32" />
                <input type="password" name="password" placeholder="Password (6-20 chars)" required minlength="6" maxlength="20" />
                <input type="file" name="image" accept="image/png,image/jpeg" />
                <button type="submit">Register</button>
                <div class="msg" id="registerMsg"></div>
            </form>

            <div class="toggle-buttons">
                <button type="button" id="showLogin" class="active">Sign In</button>
                <button type="button" id="showRegister">Create Account</button>
            </div>

        </div>

        <script>
            document.getElementById('registerForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const res = await fetch('api.php?action=register', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                const msg = document.getElementById('registerMsg');
                if (data.ok) {
                    msg.textContent = "Account created successfully! You can now log in.";
                    msg.style.color = "#4ade80";
                } else {
                    msg.textContent = data.error || "Registration failed.";
                    msg.style.color = "#f87171";
                }
            });

            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                const params = new URLSearchParams();
                for (const [k, v] of formData.entries()) params.append(k, v);
                const res = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params
                });
                const data = await res.json();
                const msg = document.getElementById('loginMsg');
                if (data.ok) {
                    msg.textContent = "Login successful! Redirecting...";
                    msg.style.color = "#4ade80";
                    setTimeout(() => window.location.href = "homepage.php", 1000);
                } else {
                    msg.textContent = data.error || "Login failed.";
                    msg.style.color = "#f87171";
                }
            });

            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const showLogin = document.getElementById('showLogin');
            const showRegister = document.getElementById('showRegister');

            showLogin.addEventListener('click', () => {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                showLogin.classList.add('active');
                showRegister.classList.remove('active');
            });

            showRegister.addEventListener('click', () => {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                showRegister.classList.add('active');
                showLogin.classList.remove('active');
            });
        </script>
    </main>
    <script src="/~bdb/bookworm/search.js"></script>

</body>

</html>