<?php
require __DIR__ . '/../../phpTools/config.php';
$db = get_db();

$user_id = $_SESSION['user_id'] ?? '';

// ---------- HANDLE RATING SUBMIT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($user_id)) {
        fail(401, 'Login required to rate');
    }

    $userId = (int)$user_id;
    $bookId = (int)($_POST['bid'] ?? null);
    $ratingVal = (int)($_POST['rating'] ?? 0);

    if (empty($bookId)) {
        fail(400, 'Book ID Required');
    }

    if ($ratingVal < 1 || $ratingVal > 5) {
        fail(400, 'Rating must be 1–5');
    }

    // one row per (user, book) 
    $stmt = $db->prepare("
            INSERT INTO ratings (book_id, user_id, rating)
            VALUES (:bid, :uid, :rating)
            ON DUPLICATE KEY UPDATE
            rating = VALUES(rating),
            creation_date = CURRENT_TIMESTAMP
        ");
    $stmt->execute([
        ':bid'    => $bookId,
        ':uid'    => $userId,
        ':rating' => $ratingVal
    ]);

    header("Location: dynBook.php?bid=" . $bookId . "&rated=1");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fail(405, 'Method Not Allowed');
}

if (empty($_GET['bid'])) {
    fail(400, 'Book ID Required');
}
$book_id = htmlspecialchars($_GET['bid']);

$page = (int)($_GET['page'] ?? 1);

if ($_GET['cid'] ?? false) {
    header("Location: dynBook.php?bid=" . $book_id . "#cid" . $_GET['cid']);
}

$stmt = $db->prepare('SELECT * FROM books WHERE book_id = ?');
$stmt->bindParam(1, $book_id, PDO::PARAM_INT);
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

// ---------- RATING DATA ----------
$avgStmt = $db->prepare("
        SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_ratings
        FROM ratings
        WHERE book_id = ?
    ");
$avgStmt->bindParam(1, $book_id, PDO::PARAM_INT);
$avgStmt->execute();
$ratingStats = $avgStmt->fetch(PDO::FETCH_ASSOC);

$userRating = null;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];

    $uStmt = $db->prepare("
            SELECT rating
            FROM ratings
            WHERE book_id = ? AND user_id = ?
        ");
    $uStmt->execute([$book_id, $uid]);
    $row = $uStmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $userRating = (int)$row['rating'];
    }
}
?>

<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Book - <?= $book['title'] ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/~bdb/bookworm/app.css">
    </head>

    <body>
        <?php require_once __DIR__ . '/../../phpTools/navbar.php' ?>

        <main>
            <section class="dynBook-main">
                <div class="bookContainer">

                    <!-- LEFT SIDE: image + title + basic info + genres -->
                    <div class="bookContainer-left">
                        <img src="<?= $book['image_path'] ?? $altPath?>" alt="Missing_Image">

                        <div class="bookMeta">
                            <h1><?= $book['title'] ?></h1>

                            <div class="bookInfo">
                                <p class="bookISBN"><?= $book['isbn'] ?></p>
                                <p class="bookAuthor"><?= $book['author'] ?></p>

                                <span class="bookPublish"
                                      data-date="<?= htmlspecialchars($book['published']) ?>">
                                    <?= htmlspecialchars($book['published']) ?>
                                </span>

                                <div class="grid">
                                    <?php foreach ($genres as $g): ?>
                                        <a href="<?= "/~bdb/bookworm/advSearch.php?genres[]=" . urlencode($g['genre']) ?>">
                                            <?= $g['genre'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: rating + scrollable summary -->
                    <div class="bookContainer-right">

                        <!-- ---------- Rating Display + Form ---------- -->
                        <div class="ratingBlock">
                            <?php
                            $avgRating = $ratingStats['avg_rating'] ? round((float)$ratingStats['avg_rating'], 1) : 0;
                            $totalRatings = (int)$ratingStats['total_ratings'];
                            ?>
                            <p class="muted">
                                Avg rating: <?= $avgRating ?> / 5 (<?= $totalRatings ?> ratings)
                            </p>

                            <?php if (!empty($_GET['rated'])): ?>
                                <div id="ratingToast" class="ratingToast">
                                    Rating submitted!
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" class="starRatingForm">
                                    <input type="hidden" name="bid" value="<?= (int)$book_id ?>">

                                    <div class="starRating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input
                                                type="radio"
                                                class="starRadio"
                                                id="star<?= $i ?>"
                                                name="rating"
                                                value="<?= $i ?>"
                                                <?= ($userRating === $i) ? 'checked' : '' ?>
                                                required>
                                            <label for="star<?= $i ?>">★</label>
                                        <?php endfor; ?>
                                    </div>

                                    <button type="submit">
                                        <?= ($userRating !== null) ? "Update Rating" : "Submit Rating" ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <p class="muted">Log in to rate this book.</p>
                            <?php endif; ?>
                        </div>

                        <div class="bookSummary">
                            <p style="white-space: pre-line">
                                <?= $book['summary'] ?>
                            </p>
                        </div>

                    </div>
                </div>
            </section>

            <section class="dynBook-comments">
                <div class="commentContainer">
                    <h2 class="commentHeader">Comments</h2>

                    <!--Generate Comments After Form!-->
                    <?php if (!empty($user_id)): ?>
                        <div class="commentForm">
                            <label for="commentText">
                                <h4>Enter Comment Here:</h4>
                            </label>
                            <textarea
                                id="commentText"
                                rows="5"
                                cols="50"
                                name="commentText"
                                class="commentTextarea"
                                placeholder="Comment Here..."
                            ></textarea>
                            <button class="commentSubmit"
                                    data-bid="<?= $book_id ?>"
                                    data-uid="<?= $_SESSION['user_id'] ?>">
                                Post Comment
                            </button>
                        </div>
                    <?php else: ?>
                        <h4>Sign-in/Register to Comment:</h4>
                    <?php endif; ?>
                    <div id="pageTop" class="pageControls"></div>
                    <div id="commentSection" class="commentSection"></div>
                    <div id="pageBottom" class="pageControls"></div>
                </div>
            </section>
            
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
            const book_id = <?= json_encode($book_id) ?>;
            const getpage = <?= json_encode($page) ?>;
            let currentPage = getpage;
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
            function initializeReplyButtons() {
                document.querySelectorAll('.replyButton').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        const form = document.querySelector(`.replyForm[data-id="${id}"]`);
                        btn.classList.add('hidden');
                        form.classList.remove('hidden');
                    });
                });
            
                document.querySelectorAll('.replyCancel').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const form = btn.closest('.replyForm');
                        const id = form.dataset.id;
                        form.classList.add('hidden');
                        form.reset();
                    
                        const rb = document.querySelector(`.replyButton[data-id="${id}"]`);
                        rb.classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.replyGroup').forEach(group => {
                    const replies = Array.from(group.querySelectorAll('.reply'));
                    if (replies.length > 3) {
                        replies.slice(3).forEach(r => r.classList.add('hidden'));
                    
                        const btn = document.createElement('button');
                        btn.textContent = `Show ${replies.length - 3} more`;
                        btn.className = 'replyShowMore';
                        btn.addEventListener('click', () => {
                            replies.slice(3).forEach(r => r.classList.remove('hidden'));
                            btn.remove();
                        });
                        group.appendChild(btn);
                    }
                });
            }
            //Render the page buttons for Comments
            function renderPageButtons(p) {
                const containers = [
                    document.getElementById("pageTop"),
                    document.getElementById("pageBottom")
                ];
            
                containers.forEach(container => {
                    container.innerHTML = "";
                    for (let i = 1; i <= p; i++) {
                        const btn = document.createElement("button");
                        btn.textContent = i;
                        btn.className = "pageButton";
                        if (i === currentPage) btn.classList.add("activePage");
                    
                        btn.addEventListener("click", () => {
                            refreshComments(book_id, i);
                        });
                    
                        container.appendChild(btn);
                    }
                });
            }
            document.addEventListener("DOMContentLoaded", () => {
                document.body.addEventListener("click", (event) => {
                    //Reply Button
                    if (event.target.classList.contains("replyButton")) {
                        const id = event.target.dataset.id;
                        const form = document.querySelector(`.replyForm[data-id="${id}"]`);
                        if (form) {
                            form.classList.remove("hidden");
                            event.target.classList.add("hidden");
                        }
                        return;
                    }
                    //Cancel Reply Button
                    if (event.target.classList.contains("replyCancel")) {
                        const id = event.target.dataset.id;
                        const form = document.querySelector(`.replyForm[data-id="${id}"]`);
                        const rb = document.querySelector(`.replyButton[data-id="${id}"]`);
                        if (form && rb) {
                            form.classList.add("hidden");
                            form.querySelector("textarea").value = "";
                            rb.classList.remove("hidden");
                        }
                        return;
                    }
                });

                document.querySelectorAll(".replyGroup").forEach(group => {
                    const replies = Array.from(group.querySelectorAll(".reply"));
                    if (replies.length > 3) {
                        // Hide replies after three
                        replies.slice(3).forEach(r => r.classList.add("hidden"));
                    
                        // Show Replies Button
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.textContent = `Show ${replies.length - 3} more repl${replies.length - 3 === 1 ? "y" : "ies"}`;
                        btn.className = "replyShowMore";
                    
                        btn.addEventListener("click", () => {
                            replies.slice(3).forEach(r => r.classList.remove("hidden"));
                            btn.remove();
                        });
                    
                        group.appendChild(btn);
                    }
                });
            
                const hash = window.location.hash;
            
                if (hash && hash.startsWith("#cid")) {
                    const targetSection = document.querySelector(hash);
                    if (targetSection) {
                        // Expand previous reply groups
                        let c = targetSection;
                        while (c) {
                            if (c.classList && c.classList.contains("replyGroup")) {
                                c.querySelectorAll(".reply.hidden").forEach(r => r.classList.remove("hidden"));
                                const btn = c.querySelector(".replyShowMore");
                                if (btn) btn.remove();
                            }
                            c = c.parentElement;
                        }
                        
                        // Scroll into view
                        setTimeout(() => {
                            targetSection.scrollIntoView({ block: "center", behavior: "smooth" });
                        }, 0);
                    }
                }
            });
            async function refreshComments(bookId, page = 1) {
                currentPage = page;
                const formData = new FormData();
                formData.append("bid", bookId);
                formData.append("page", page);

                const res = await fetch('/~bdb/bookworm/commentBridge.php?t=f', {
                    method: "POST",
                    body: formData
                });
                const resText = await res.text();
                const match = resText.match(/<!--PAGES:(\d+)-->/);
                const total = match ? parseInt(match[1], 10) : 1;
                if (page < 1) {
                    currentPage = 1;
                    refreshComments(book_id, currentPage);
                    return;
                }
                if (page > total) {
                    currentPage = total;
                    refreshComments(book_id, currentPage);
                    return;
                }
                const html = resText.replace(/<!--PAGES:\d+-->/, "");
                document.getElementById("commentSection").innerHTML = html;
                anchor = (html.comment_id !== undefined) ? html.comment_id : null;
            
                //re-init page buttons  
                renderPageButtons(total);
                // Re-init reply buttons and toggles
                initializeReplyButtons();

                //Change URL without reload
                const newUrl = `?bid=${book_id}&page=${page}`;
                window.history.replaceState({}, "", newUrl);

                if (anchor) {
                    const elem = document.querySelector(`#cid${anchor}`);
                    if (elem) {
                        setTimeout(() => {
                            elem.scrollIntoView({
                                behavior: "smooth",
                                block: "start"
                            });
                        }, 60);
                    }
                }
            }
            refreshComments(book_id, currentPage);
            const hash = window.location.hash;
            if (hash || hash.startsWith("#cid")) {
                const el = document.querySelector(hash);
                if (el) {
                    // Expand hidden replies if necessary
                    el.closest(".replyGroup")
                      ?.querySelectorAll(".reply.hidden")
                      .forEach(r => r.classList.remove("hidden"));

                    // Scroll to the element
                    setTimeout(() => {
                        el.scrollIntoView({ behavior: "smooth", block: "center" });
                    }, 50);
                }
            }
            //Send forms without reload
            document.body.addEventListener("click", async event => {
                if (!event.target.classList.contains("commentSubmit")) return;

                const comment = event.target;
                comment.disabled = true;

                const bid = comment.dataset.bid;
                const uid = comment.dataset.uid;
                const text = document.querySelector("#commentText").value.trim();

                if (!text) {
                    alert("Comment cannot be empty.");
                    comment.disabled = false;
                    return;
                }
            
                const formData = new FormData();
                formData.append("bid", bid);
                formData.append("uid", uid);
                formData.append("commentText", text);
            
                const res = await fetch("/~bdb/bookworm/commentBridge.php?t=c", {
                    method: "POST",
                    body: formData
                });
            
                const out = await res.json();
                console.log("Top-level comment result:", out);
                const anchor = (out.comment_id !== undefined) ? out.comment_id : null;
            
                document.querySelector("#commentText").value = "";
            
                setTimeout(() => refreshComments(bid, currentPage), 50);
                comment.disabled = false;
            });
            document.body.addEventListener("click", async event => {
                if (!event.target.classList.contains("replySubmit")) return;

                const reply = event.target;
                reply.disabled = true;

                const form = reply.closest(".replyForm");

                const text = form.querySelector("textarea").value.trim();
                const bid = reply.dataset.bid;
                const uid = reply.dataset.uid;
                const pid = reply.dataset.pid;

                if (!text) {
                    alert("Reply cannot be empty.");
                    reply.disabled = false;
                    return;
                }
            
                const formData = new FormData();
                formData.append("bid", bid);
                formData.append("uid", uid);
                formData.append("pid", pid);
                formData.append("commentText", text);
            
                const res = await fetch("/~bdb/bookworm/commentBridge.php?t=c", {
                    method: "POST",
                    body: formData
                });
            
                const out = await res.json();
                console.log("Reply result:", out);
                const anchor = (out.comment_id !== undefined) ? out.comment_id : null; 
            
                // reset the reply form UI
                form.classList.add("hidden");
                form.querySelector("textarea").value = "";
            
                const btn = document.querySelector(`.replyButton[data-id="${pid}"]`);
                if (btn) btn.classList.remove("hidden");
            
                setTimeout(() => refreshComments(bid, currentPage), 50);
                reply.disabled = false;
                if (anchor) {
                    const elem = document.querySelector(`#cid${anchor}`);
                    if (elem) {
                        setTimeout(() => {
                            elem.scrollIntoView({
                                behavior: "smooth",
                                block: "start"
                            });
                        }, 60);
                    }
                }
            });
            document.body.addEventListener("click", async (event) => {
                if (!event.target.classList.contains("deleteButton")) return;

                const ok = await confirmToast("Delete this comment?");
                if (!ok) return;

                const cid = event.target.dataset.cid;
                const reason = event.target.dataset.reason;

                const formData = new FormData();
                formData.append("cid", cid);
                formData.append("reason", reason);

                await fetch("/~bdb/bookworm/commentBridge.php?t=d", {
                    method: "POST",
                    body: formData
                });
            
                setTimeout(() => refreshComments(book_id, currentPage), 50);
            });
            document.body.addEventListener("click", async (event) => {
                if (!event.target.classList.contains("restoreButton")) return;

                const ok = await confirmToast("Restore this comment?");
                if (!ok) return;

                const cid = event.target.dataset.cid;

                const formData = new FormData();
                formData.append("cid", cid);

                await fetch("/~bdb/bookworm/commentBridge.php?t=r", {
                    method: "POST",
                    body: formData
                });
            
                setTimeout(() => refreshComments(book_id, currentPage), 50);
            });
            // Find all date spans
            document.querySelectorAll('.bookPublish').forEach(span => {
                const raw = span.dataset.date;
                const date = new Date(raw);

                const formatted = new Intl.DateTimeFormat(navigator.language, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }).format(date);

                span.textContent = formatted;
            });
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

            const ratingToast = document.getElementById('ratingToast');
            if (ratingToast) {
                setTimeout(() => {
                    ratingToast.classList.add('hide');
                }, 1500);
            }

            const starForm = document.querySelector('.starRatingForm');
            if (starForm) {
                starForm.addEventListener('submit', () => {
                    const btn = starForm.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.textContent = "Submitting...";
                    }
                });
            }
            setInterval(() => {
                if (document.querySelector(".replyForm:not(.hidden)")) {
                    return;
                }
                if (document.hidden) {
                    return;
                }
                refreshComments(book_id, currentPage);
            }, 10000);
        </script>
    </body>
</html>