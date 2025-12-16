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

try {
    // Determine target profile user
    if ($viewUsername) {
        // Lookup by username
            $stmt = $db->prepare("SELECT * FROM users WHERE username LIKE :uname AND is_active = 1");
            $stmt->execute([':uname' => $viewUsername]);
            $user = $stmt->fetch();

            $stmt = $db->prepare("SELECT * FROM admins WHERE user_id = :uid");
            $stmt->execute([":uid"=> (int)$user['user_id']]);

            $admin = ($stmt->fetch(PDO::FETCH_ASSOC))['admin_id'] ?? null;
    } else if ($viewUserId) {
        // Lookup by user_id
            $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :uid AND is_active = 1");
            $stmt->execute([':uid' => $viewUserId]);
            $user = $stmt->fetch();

            $stmt = $db->prepare("SELECT * FROM admins WHERE user_id = :uid");
            $stmt->execute([":uid"=> (int)$user['user_id']]);

            $admin = ($stmt->fetch(PDO::FETCH_ASSOC))['admin_id'] ?? null;
    } else if ($sessionUserId) {
        header("Location: /~bdb/bookworm/profile.php/?uid=" . $sessionUserId);
    } else {
        header("Location: signin.php");
        exit;
    }
} catch (PDOException $e) {
    fail(500, 'Error: ' . $e->getMessage());
}

if (!$user) {
    echo "<h2>User not found</h2>";
    exit;
}

$profileUserId = (int)$user['user_id'];
$profileRole = $admin ? true : false;
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
        $upd->execute([':p' => $newHash, ':uid' => $profileUserId]);

        session_destroy();
        header("Location: signin.php");
        exit;
    }
}


// PROFILE PIC UPLOAD
if ($canEdit && isset($_FILES['newpic'])) {

    $file = $_FILES['newpic'];

    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] < 4 * 1024 * 1024) {

        $info = new finfo(FILEINFO_MIME_TYPE);
        $mime = $info->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (isset($allowed[$mime])) {

            $ext = $allowed[$mime];
            $fname = bin2hex(random_bytes(16)) . "." . $ext;
            //directory change here
            $uploadDir = __DIR__ . "/../images/users";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $dest = $uploadDir . DIRECTORY_SEPARATOR . $fname;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                //public url
                $rel = "/~bdb/images/users/" . $fname;


                $upd = $db->prepare("UPDATE users SET image_path = :p WHERE user_id = :u");
                $upd->execute([':p' => $rel, ':u' => $profileUserId]);

                header("Location: profile.php");
                exit;
            }
        }
    }
}



// LOAD COMMENTS + RATINGS + SHADOW COMMENTS
$comments = $db->prepare("
    SELECT C.book_id, C.comment_id, C.comment_text, C.creation_date, B.title
    FROM comments C JOIN books B ON C.book_id = B.book_id WHERE C.user_id = :uid
    ORDER BY C.creation_date DESC
");
$comments->execute([':uid' => $profileUserId]);
$comments = $comments->fetchAll();

$ratings = $db->prepare("
    SELECT R.book_id, R.rating, R.creation_date, B.title FROM ratings R
    JOIN books B ON R.book_id = B.book_id WHERE R.user_id = :uid
    ORDER BY R.creation_date DESC
");
$ratings->execute([':uid' => $profileUserId]);
$ratings = $ratings->fetchAll();

$shadows = $db->prepare("
    SELECT S.book_id, S.comment_id, S.comment_text,
    S.creation_date, S.deletion_date, B.title FROM shadowcomments S 
    JOIN books B ON S.book_id = B.book_id WHERE S.user_id = :uid
    ORDER BY S.creation_date DESC
");
$shadows->execute([':uid' => $profileUserId]);
$shadows = $shadows->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile – <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/~bdb/bookworm/app.css">
</head>

<body>
    <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>

    <main class="profile-container">

        <!-- Profile Header -->
        <section class="profile-header">
            <img src="<?= htmlspecialchars($user['image_path'] ?? $altPath) ?>">

            <div>
                <h2><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></h2>
                <label>Joined: </label>
                <span class="profileDate muted" data-date="<?= htmlspecialchars($user['creation_date']) ?>"><?= htmlspecialchars($user['creation_date']) ?></span>

                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin' && $profileRole): ?>
                    <span class="profile-role">ADMIN</span>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tabs   -->
        <div class="tabs">
            <div class="tab-btn" data-tab="comments">View Comments</div>
            <div class="tab-btn" data-tab="ratings">View Ratings</div>
            <?php if ($canEdit): ?>
                <div class="tab-btn" data-tab="restore">Restore Comments</div>
                <div class="tab-btn" data-tab="edit">Edit Profile</div>
            <?php endif; ?>
        </div>

        <!-- COMMENTS -->
        <section id="comments" class="tab-content">
            <h3>User Comments</h3>

            <?php if (!$comments): ?>
                <p class="muted">No comments yet.</p>
            <?php else: ?>
                <div class="user-comments-list" style="display:flex; flex-direction:column; gap:15px;">
                    <?php foreach ($comments as $c): ?>
                        <a class="cardLink" href="<?= '/~bdb/bookworm/dynBook.php?bid=' . $c['book_id']?>">
                        <div class="card" id="<?= $c['comment_id'] ?>">
                            <strong><?= htmlspecialchars($c['title']) ?></strong>
                            <p><?= $c['comment_text'] ?></p>
                            <span class="commentDate muted" data-date="<?= htmlspecialchars($c['creation_date']) ?>">
                                <?= htmlspecialchars($c['creation_date']) ?>
                            </span>
                        </div>
                        </a>
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
                        <a class="cardLink" href=<?= "/~bdb/bookworm/dynBook.php/?bid=" . $r['book_id'] ?>>
                            <div class="card" style="padding:15px;">
                                <strong><?= htmlspecialchars($r['title']) ?></strong>
                                <p>Rated: <?= htmlspecialchars($r['rating']) ?>/5</p>
                                <span class="commentDate muted" data-date="<?= htmlspecialchars($r['creation_date']) ?>">
                                    <?= htmlspecialchars($r['creation_date']) ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- EDIT PROFILE -->
        <?php if ($canEdit): ?>
            <section id="restore" class="tab-content profile-restore">
                <h3>Restore Comments</h3>
                <?php if (!$shadows): ?>
                    <p class="muted">Zero Deleted Comments Found</p>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <?php foreach ($shadows as $s): ?>
                            <button class="profile-restore-btn" data-cid="<?= $s['comment_id'] ?>" type="button">
                                <div class="card" id="<?= $s['comment_id'] ?>">
                                    <strong><?= htmlspecialchars($s['title']) ?></strong>
                                    <p><?= $s['comment_text'] ?></p>
                                    <label style="font-size: 0.78rem; color: #9ca3af;" class="muted">Created: </label>
                                    <span class="commentDate muted" data-date="<?= htmlspecialchars($s['creation_date']) ?>">
                                       <?= htmlspecialchars($s['creation_date']) ?>
                                    </span><br>
                                    <label style="font-size: 0.78rem; color: #9ca3af;" class="muted">Deleted: </label>
                                    <span class="commentDate muted" data-date="<?= htmlspecialchars($s['deletion_date']) ?>">
                                        <?= htmlspecialchars($s['deletion_date']) ?>
                                    </span>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <section id="edit" class="tab-content profile-edit">
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

    <div id="confirmOverlay" class="confirmOverlay hidden"></div>
        <div id="confirmToast" class="confirmBox hidden">
            <div class="confirmBox-content">
                <p id="confirmToast-message">Are you sure?</p>
                <div class="confirmBox-buttons">
                    <button id="confirmToast-yes">Yes</button>
                    <button id="confirmToast-no">No</button>
                </div>
            </div>
        </div>

    <script>
        async function reloadUserComments() {
            const uid = new URLSearchParams(location.search).get("uid");
            const response = await fetch(`/~bdb/bookworm/profile.php/?uid=${uid}`, {
                method: "GET",
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });
        
            const text = await response.text();
        
            // Create a temporary DOM to extract new comments HTML
            const temp = document.createElement("div");
            temp.innerHTML = text;
        
            const newC = temp.querySelector(".user-comments-list");
            const oldC = document.querySelector(".user-comments-list");
        
            if (newC && oldC) {
                oldC.innerHTML = newC.innerHTML;
            }
        
            // Re-format timestamps again
            document.querySelectorAll('.commentDate').forEach(span => {
                const raw = span.dataset.date;
                const date = new Date(raw);
                const formatted = new Intl.DateTimeFormat(navigator.language, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: "numeric",
                    minute: "numeric"
                }).format(date);
                span.textContent = formatted;
            });
        }
        // CONFIRM POPUP
        function confirmToast(message = "Are you sure?") {
            return new Promise(resolve => {
                const modal = document.getElementById("confirmToast");
                const overlay = document.getElementById("confirmOverlay");
                const msg = document.getElementById("confirmToast-message");
                const yes = document.getElementById("confirmToast-yes");
                const no  = document.getElementById("confirmToast-no");
            
                msg.textContent = message;
            
                overlay.classList.remove("hidden");
                modal.classList.remove("hidden");
            
                requestAnimationFrame(() => {
                    overlay.classList.add("show");
                    modal.classList.add("show");
                });
            
                const cleanup = (result) => {
                    overlay.classList.remove("show");
                    modal.classList.remove("show");
                
                    // hide after animation ends
                    setTimeout(() => {
                        overlay.classList.add("hidden");
                        modal.classList.add("hidden");
                    }, 160);
                
                    yes.removeEventListener("click", yesHandler);
                    no.removeEventListener("click", noHandler);
                    resolve(result);
                };
            
                const yesHandler = () => cleanup(true);
                const noHandler  = () => cleanup(false);
            
                yes.addEventListener("click", yesHandler);
                no.addEventListener("click", noHandler);
            });
        }
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
        document.querySelectorAll('.commentDate').forEach(span => {
            const raw = span.dataset.date;
            const date = new Date(raw);

            //Format to the user's locale
            const formatted = new Intl.DateTimeFormat(navigator.language, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: "numeric",
                minute: "numeric"
            }).format(date);

            span.textContent = formatted;
        });
        document.querySelectorAll('.profileDate').forEach(span => {
            const raw = span.dataset.date;
            const date = new Date(raw);

            //Format to the user's locale
            const formatted = new Intl.DateTimeFormat(navigator.language, {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }).format(date);

            span.textContent = formatted;
        });
        const restoreBtn = document.querySelectorAll(".profile-restore-btn");

        restoreBtn.forEach(b => {
            b.addEventListener("click", async (event) => {
                const button = event.target.closest(".profile-restore-btn");
                if (!button) return;
            
                const confirm = await confirmToast("Restore this Comment?");
                if (!confirm) return;
            
                const cid = button.dataset.cid;
            
                const formData = new FormData();
                formData.append("cid", cid);
            
                await fetch("/~bdb/bookworm/commentBridge.php?t=r", {
                    method: "POST",
                    body: formData
                });
            
                button.remove();
                reloadUserComments();
            });
        });
    </script>
</body>
</html>