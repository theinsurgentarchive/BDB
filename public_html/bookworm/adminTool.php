<?php
require '/home/stu/bdb/phpTools/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

$db      = get_db();
$userId  = (int)$_SESSION['user_id'];
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (!$isAdmin) {
    // not an admin -> bounce them away
    header("Location: homepage.php");
    exit;
}

$adminMsg = "";

// ---------- ADMIN: APPROVE / DENY REQUEST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Approve
    if (isset($_POST['approve_form_id'])) {
        $formId = (int)$_POST['approve_form_id'];

        try {
            $admStmt = $db->prepare(
                "SELECT admin_id FROM admins WHERE user_id = :uid"
            );
            $admStmt->execute([':uid' => $userId]);
            $adm = $admStmt->fetch(PDO::FETCH_ASSOC);

            if (!$adm) {
                $adminMsg = "You are not registered as an admin.";
            } else {
                $adminId = (int)$adm['admin_id'];

                $call = $db->prepare(
                    "CALL formToBook(:fid, :aid, @new_book_id)"
                );
                $call->execute([
                    ':fid' => $formId,
                    ':aid' => $adminId,
                ]);

                $row = $db->query("SELECT @new_book_id AS book_id")
                    ->fetch(PDO::FETCH_ASSOC);

                if ($row && $row['book_id']) {
                    $adminMsg = "Request #{$formId} approved (book ID " . (int)$row['book_id'] . ").";
                } else {
                    $adminMsg = "Request #{$formId} approved.";
                }
            }
        } catch (Exception $e) {
            $adminMsg = "Error approving request: " . $e->getMessage();
        }
    }

    // Deny
    if (isset($_POST['deny_form_id'])) {
        $formId = (int)$_POST['deny_form_id'];

        try {
            $db->beginTransaction();

            $delGenres = $db->prepare(
                "DELETE FROM formgenres WHERE form_id = :form_id"
            );
            $delGenres->execute([':form_id' => $formId]);

            $delForm = $db->prepare(
                "DELETE FROM forms WHERE form_id = :form_id"
            );
            $delForm->execute([':form_id' => $formId]);

            $db->commit();
            $adminMsg = "Request #{$formId} has been denied and removed.";
        } catch (Exception $e) {
            $db->rollBack();
            $adminMsg = "Error denying request: " . $e->getMessage();
        }
    }
}

// ---------- LOAD ALL PENDING REQUESTS ----------
$query = $db->query(
    "SELECT
        f.form_id,
        f.isbn,
        f.title,
        f.author,
        f.published,
        f.summary,
        f.creation_date,
        f.image_path,
        u.username,
        GROUP_CONCAT(fg.genre ORDER BY fg.genre SEPARATOR ', ') AS genres
     FROM forms AS f
     JOIN users AS u ON u.user_id = f.user_id
     LEFT JOIN formgenres AS fg ON fg.form_id = f.form_id
     WHERE f.approve_date IS NULL
     GROUP BY f.form_id
     ORDER BY f.creation_date DESC"
);
$allForms = $query->fetchAll();

// ---------- TOP ACTIVE USERS ----------
$activeUsers = [];
try {
    $activeStmt = $db->prepare("CALL topActiveUsers(?)");
    $activeStmt->execute([30]);                 // last 30 days
    $activeUsers = $activeStmt->fetchAll(PDO::FETCH_ASSOC);
    $activeStmt->closeCursor();
} catch (Exception $e) {
    // if it fails, just show nothing (don’t break the page)
    $activeUsers = [];
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Book – Admin Tool</title>
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
    <main>

        <section>
            <h2>Pending book requests</h2>

            <?php if ($adminMsg !== ""): ?>
                <p class="muted"><?= htmlspecialchars($adminMsg) ?></p>
            <?php endif; ?>

            <?php if (!$allForms): ?>
                <p class="muted">No pending requests.</p>
            <?php else: ?>
                <div class="rows">
                    <?php foreach ($allForms as $f): ?>
                        <div class="row">
                            <div>
                                <div class="title">
                                    <?= htmlspecialchars($f['title']) ?> (<?= htmlspecialchars($f['isbn']) ?>)
                                </div>
                                <div class="author">
                                    by <?= htmlspecialchars($f['author']) ?>
                                </div>
                                <div class="muted">
                                    Genres: <?= htmlspecialchars($f['genres'] ?? '') ?>
                                </div>
                                <?php if (!empty($f['image_path'])): ?>
                                    <div class="muted">
                                        Image: <?= htmlspecialchars($f['image_path']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div class="muted">
                                    Requested by: <?= htmlspecialchars($f['username']) ?>
                                </div>
                                <div class="muted">
                                    Submitted: <?= htmlspecialchars($f['creation_date']) ?>
                                </div>

                                <!-- Approve -->
                                <form action="adminTool.php" method="POST" style="margin-top:8px;">
                                    <input type="hidden" name="approve_form_id" value="<?= (int)$f['form_id'] ?>">
                                    <button type="submit">Approve</button>
                                </form>

                                <!-- Deny -->
                                <form action="adminTool.php" method="POST" style="margin-top:4px;">
                                    <input type="hidden" name="deny_form_id" value="<?= (int)$f['form_id'] ?>">
                                    <button type="submit">Deny</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>


        <section style="margin-top:30px;">
            <h2>Top active users (last 30 days)</h2>

            <?php if (!$activeUsers): ?>
                <p class="muted">No activity yet.</p>
            <?php else: ?>
                <div class="rows">
                    <?php foreach ($activeUsers as $u): ?>
                        <div class="row">
                            <div class="title"><?= htmlspecialchars($u['username']) ?></div>
                            <div class="muted">
                                Comments: <?= (int)$u['total_comments'] ?>
                                &nbsp;|&nbsp;
                                Ratings: <?= (int)$u['total_ratings'] ?>
                                &nbsp;|&nbsp;
                                Total: <?= (int)$u['total_activity'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </main>
    <script src="/~bdb/bookworm/search.js"></script>

</body>

</html>