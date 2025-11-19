<?php
session_start(); 

require_once '/home/stu/bdb/phpTools/config.php';
$db = get_db();

// Session data
$sessionUserId   = $_SESSION['user_id'] ?? null;
$sessionUsername = $_SESSION['username'] ?? null;

// Viewing another user?
$viewUsername = $_GET['username'] ?? null;
$viewUserId   = $_GET['uid'] ?? null;

// Determine target profile user
if ($viewUsername) {
    // Lookup by username
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :uname AND is_active = 1");
    $stmt->execute([':uname' => $viewUsername]);
    $user = $stmt->fetch();

} else if ($viewUserId) {
    // Lookup by user_id
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :uid AND is_active = 1");
    $stmt->execute([':uid' => $viewUserId]);
    $user = $stmt->fetch();

} else if ($sessionUserId) {
    // Fallback to your own profile
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :uid");
    $stmt->execute([':uid' => $sessionUserId]);
    $user = $stmt->fetch();

} else {
    header("Location: signin.php");
    exit;
}

if (!$user) {
    echo "<h2>User not found</h2>";
    exit;
}

$profileUserId = (int)$user['user_id'];
$canEdit = ($sessionUserId === $profileUserId);


// PASSWORD UPDATE

if ($canEdit && isset($_POST['ChangePassword'])) {

    $oldpw = $_POST['oldpw'] ?? '';
    $newpw = $_POST['newpw'] ?? '';

    $stmt = $db->prepare("SELECT password FROM users WHERE user_id = :uid");
    $stmt->execute([':uid' => $profileUserId]);
    $row = $stmt->fetch();

    if (!$row) {
        $pw_error = "User record missing.";
    } else if (!password_verify($oldpw, $row['password'])) {
        $pw_error = "Your old password is incorrect.";
    } else {
        $newHash = password_hash($newpw, PASSWORD_DEFAULT);

        $upd = $db->prepare("UPDATE users SET password = :p WHERE user_id = :uid");
        $upd->execute([':p'=>$newHash, ':uid'=>$profileUserId]);

        session_destroy();
        header("Location: signin.php");
        exit;
    }
}


// BIO UPDATE

if ($canEdit && isset($_POST['update_bio'])) {

    $bio = trim($_POST['bio'] ?? "");
    $bio = htmlspecialchars($bio, ENT_QUOTES, 'UTF-8');

    $upd = $db->prepare("UPDATE users SET bio = :b WHERE user_id = :uid");
    $upd->execute([':b' => $bio, ':uid' => $profileUserId]);

    header("Location: profileTester.php");
    exit;
}



// PROFILE PIC UPLOAD

if ($canEdit && isset($_FILES['newpic'])) {

    $file = $_FILES['newpic'];

    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] < 4*1024*1024) {

        $info = new finfo(FILEINFO_MIME_TYPE);
        $mime = $info->file($file['tmp_name']);
        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png'];

        if (isset($allowed[$mime])) {

            $ext = $allowed[$mime];
            $fname = bin2hex(random_bytes(16)) . "." . $ext;
			//directory change here
            $uploadDir = __DIR__ . "/home/stu/bdb/images/users";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $dest = $uploadDir . $fname;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
				//public url
                $rel = "/~bdb/images/users/" . $fname;

                $upd = $db->prepare("UPDATE users SET image_path = :p WHERE user_id = :u");
                $upd->execute([':p'=>$rel, ':u'=>$profileUserId]);

                header("Location: profileTester.php");
                exit;
            }
        }
    }
}



// LOAD COMMENTS + RATINGS

$comments = $db->prepare("
    SELECT c.comment_text, c.creation_date, b.title 
    FROM comments c
    JOIN books b ON c.book_id = b.book_id
    WHERE c.user_id = :uid
    ORDER BY c.creation_date DESC
");
$comments->execute([':uid' => $profileUserId]);
$comments = $comments->fetchAll();

$ratings = $db->prepare("
    SELECT r.rating, r.creation_date, b.title
    FROM ratings r
    JOIN books b ON r.book_id = b.book_id
    WHERE r.user_id = :uid
    ORDER BY r.creation_date DESC
");
$ratings->execute([':uid' => $profileUserId]);
$ratings = $ratings->fetchAll();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Profile – <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="app.css">

    <style>
        .tabs { display:flex; gap:20px; margin-bottom:20px; }
        .tab-btn {
            padding:10px 18px;
            border-radius:8px;
            background:#1d2338;
            color:#cbd5e1;
            cursor:pointer;
        }
        .tab-btn.active { background:#3b479c; color:white; }
        .tab-content { display:none; }
        .tab-content.active { display:block; }
    </style>
</head>

<body>

<header>
    <div class="brand">
        <h1>
            <a href="homepage.php" class="brand-link" style="color:inherit;text-decoration:none;">
                Book
            </a>
        </h1>
    </div>

    <nav aria-label="Primary">
        <a class="tab" href="search.php">Search</a>

        <?php if ($sessionUserId): ?>
            <a class="tab" href="profileTester.php" aria-current="page">Profile</a>
            <span class="nav-welcome">
                Welcome, <?= htmlspecialchars($sessionUsername, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a class="tab" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="tab" href="signin.php">Login / Create</a>
        <?php endif; ?>

        <a class="tab" href="top20.php">Top 20 Books</a>
        <a class="tab" href="about.php">About</a>
    </nav>
</header>

<main class="profile-container">

    <!-- Profile Header -->
    <section class="profile-header" style="display:flex; gap:20px; align-items:center; margin-bottom:25px;">
        <img 
            src="<?= htmlspecialchars($user['image_path'] ?: '/~bdb/images/default_profile.png', ENT_QUOTES, 'UTF-8') ?>" 
            style="width:120px; height:120px; object-fit:cover; border-radius:100px; border:2px solid #3a3f5c;">
        
        <div>
            <h2><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="muted">Joined: <?= htmlspecialchars($user['creation_date']) ?></p>

            <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <span style="padding:4px 8px; background:#8b0000; color:white; border-radius:6px;">
                    ADMIN
                </span>
            <?php endif; ?>
        </div>
    </section>

    <!-- Tabs -->
    <div class="tabs">
        <div class="tab-btn active" data-tab="about">About</div>
        <div class="tab-btn" data-tab="comments">Comments</div>
        <div class="tab-btn" data-tab="ratings">Ratings</div>
        <?php if ($canEdit): ?>
            <div class="tab-btn" data-tab="edit">Edit Profile</div>
        <?php endif; ?>
    </div>

    <!-- ABOUT -->
    <section id="about" class="tab-content active">
        <h3>About</h3>
		<!-- We need to alter user table and add bio for this to work--!>
        <p>
            <?php if ($user['bio'] !== null && $user['bio'] !== ''): ?>
                <?= nl2br(htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8')) ?>
            <?php else: ?>
                <span class="muted">No bio yet.</span>
            <?php endif; ?>
        </p>

        <?php if ($canEdit): ?>
            <form method="POST">
                <textarea name="bio" rows="4" maxlength="500" style="width:100%;"><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <button class="tab" type="submit" name="update_bio">Update Bio</button>
            </form>
        <?php endif; ?>
    </section>

    <!-- COMMENTS -->
    <section id="comments" class="tab-content">
        <h3>User Comments</h3>

        <?php if (!$comments): ?>
            <p class="muted">No comments yet.</p>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($comments as $c): ?>
                    <div class="card" style="padding:15px;">
                        <strong><?= htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <p><?= htmlspecialchars($c['comment_text'], ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="muted"><?= htmlspecialchars($c['creation_date'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- RATINGS -->
    <section id="ratings" class="tab-content">
        <h3>User Ratings</h3>

        <?php if (!$ratings): ?>
            <p class="muted">No ratings yet.</p>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:15px;">
                <?php foreach ($ratings as $r): ?>
                    <div class="card" style="padding:15px;">
                        <strong><?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <p>Rated: <?= htmlspecialchars($r['rating'], ENT_QUOTES, 'UTF-8') ?>/5</p>
                        <span class="muted"><?= htmlspecialchars($r['creation_date'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- EDIT PROFILE -->
    <?php if ($canEdit): ?>
    <section id="edit" class="tab-content">
        <h3>Edit Profile</h3>

        <!-- Upload Profile Picture -->
        <form method="POST" enctype="multipart/form-data" style="margin-bottom:20px;">
            <label><strong>Change Profile Picture</strong></label><br>
            <input type="file" name="newpic" accept="image/jpeg,image/png">
            <button type="submit" class="tab">Upload</button>
        </form>

        <!-- Change Password -->
        <?php if (!empty($pw_error)): ?>
            <p style="color:red;"><?= htmlspecialchars($pw_error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="POST" style="display:flex; flex-direction:column; gap:10px; max-width:300px;">
            <label><strong>Change Password</strong></label>
            <input type="password" name="oldpw" placeholder="Old password" required>
            <input type="password" name="newpw" placeholder="New password" required minlength="6">
            <button name="ChangePassword" value="1" type="submit" class="tab">Update Password</button>
        </form>
    </section>
    <?php endif; ?>

</main>

<script>
    // TAB SWITCHING
    const tabs = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(t => {
        t.addEventListener("click", () => {
            tabs.forEach(b => b.classList.remove("active"));
            t.classList.add("active");

            contents.forEach(c => {
                c.classList.remove("active");
                if (c.id === t.dataset.tab) c.classList.add("active");
            });
        });
    });
</script>

</body>
</html>
