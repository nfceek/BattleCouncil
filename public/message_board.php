<?php
$pageClass = 'page-message-board';
$pageTitle = "War Room Message Board";
$pageCss = "msg";

require_once __DIR__ . '/../includes/header.php';

/**
 * FETCH POSTS
 */
$stmt = $pdo->query("
    SELECT mb.*, u.username
    FROM message_board mb
    JOIN users u ON u.id = mb.user_id
    ORDER BY mb.created_at DESC
    LIMIT 50
");

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="container-lead">
        War Room Message Board
    </div>

    <div class="bc-grid msg-grid">
        <div class="bc-col-4"></div>
        <div class="bc-col-4">
            <div class="bc-card">


                <div class="bc-card-body msg-board">

                    <!-- POST FORM -->
                    <form method="POST" action="/public/message_post.php" class="msg-form">

                        <input type="text" name="title" class="bc-input" placeholder="Title (optional)">

                        <textarea name="message"
                                  class="bc-input msg-textarea"
                                  maxlength="500"
                                  required
                                  placeholder="Write your message..."></textarea>

                        <input type="text" name="tag" class="bc-input" placeholder="Clan / Kingdom tag (optional)">

                       <button class="bc-btn msg-board-btn">
                            <i class="fa-solid fa-feather"></i>
                            Post Message
                        </button>

                    </form>

                    <hr>

                    <!-- POSTS -->
                    <div class="msg-feed">

                        <?php foreach ($posts as $p): ?>

                            <div class="msg-post">

                                <div class="msg-meta">
                                    <strong><?= htmlspecialchars($p['username']) ?></strong>
                                    <span><?= $p['created_at'] ?></span>
                                </div>

                                <?php if (!empty($p['title'])): ?>
                                    <div class="msg-title">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="msg-body">
                                    <?= nl2br(htmlspecialchars($p['message'])) ?>
                                </div>

                                <?php if (!empty($p['tag'])): ?>
                                    <div class="msg-tag">
                                        <?= htmlspecialchars($p['tag']) ?>
                                    </div>
                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>
        <div class="bc-col-4"></div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>