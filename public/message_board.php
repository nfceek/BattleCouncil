<?php
$pageClass = 'page-message-board';
$pageTitle = "The Crow - Message Board";
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
        The Crow - Message Board
    </div>

    <div class="bc-grid msg-grid">
        <div class="bc-col-4"></div>
        <div class="bc-col-4">
            <div class="bc-card">


                <div class="bc-card-body msg-board">

                    <!-- POST FORM -->
                    <div class="msg-tabs">
                        <button class="msg-tab active" data-tab="tips">Tips</button>
                        <button class="msg-tab" data-tab="questions">Questions</button>
                    </div>

                    <div id="tab-tips" class="msg-tab-content active">

                        <?php if (canPostTip($_SESSION)): ?>
                            <form method="POST" action="/public/message_post.php" class="msg-form">
                                <input type="hidden" name="type" value="tip">

                                <input type="text" name="title" class="bc-input" placeholder="Tip title">

                                <textarea name="message" class="bc-input msg-textarea" required></textarea>

                                <button class="bc-btn msg-board-btn">
                                    <i class="fa-solid fa-lightbulb"></i> Post Tip
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="msg-locked">
                                Tips are available to officers and above.
                            </div>
                        <?php endif; ?>

                    </div>

                    <div id="tab-questions" class="msg-tab-content">

                        <form method="POST" action="/public/message_post.php" class="msg-form">
                            <input type="hidden" name="type" value="question">

                            <textarea name="message" class="bc-input msg-textarea" required
                                placeholder="Ask a question..."></textarea>

                            <button class="bc-btn msg-board-btn">
                                <i class="fa-solid fa-question"></i> Ask Question
                            </button>
                        </form>

                    </div>

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