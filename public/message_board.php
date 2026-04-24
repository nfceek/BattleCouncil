<?php
$pageClass = 'page-message-board';
$pageTitle = "The Crow - Message Board";
$pageCss = "msg";

require_once __DIR__ . '/../includes/header.php';

$user = getCurrentUser();

/**
 * ACTIVE TAB (SAFE)
 */
$tab = $_GET['tab'] ?? 'tips';
$tab = in_array($tab, ['tips','questions']) ? $tab : 'tips';

/**
 * FETCH POSTS (UNIFIED TABLE MODEL)
 */
$stmt = $pdo->prepare("
    SELECT mb.*, u.username
    FROM message_board mb
    JOIN users u ON u.id = mb.user_id
    WHERE mb.type = ?
    ORDER BY 
        CASE 
            WHEN mb.type = 'tip' THEN (mb.rating_sum / NULLIF(mb.rating_count,0))
            ELSE mb.votes
        END DESC,
        mb.created_at DESC
    LIMIT 50
");

$stmt->execute([$tab]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="container-lead">
        The Crow - Message Central
    </div>

    <div class="bc-grid msg-grid">
        <div class="bc-col-4"></div>

        <div class="bc-col-4">
            <div class="bc-card">
                <div class="bc-card-body msg-board">

                    <!-- ======================
                         TABS (SERVER CONTROLLED)
                    ======================= -->
                    <div class="msg-tabs">
                        <a class="msg-tab <?= $tab === 'tips' ? 'active' : '' ?>"
                           href="?tab=tips">
                            <i class="fa-solid fa-lightbulb"></i> Tips
                        </a>

                        <a class="msg-tab <?= $tab === 'questions' ? 'active' : '' ?>"
                           href="?tab=questions">
                            <i class="fa-solid fa-question"></i> Questions
                        </a>
                    </div>

                    <!-- ======================
                         TIP INPUT
                    ======================= -->
                    <?php if ($tab === 'tips'): ?>

                        <?php if (canPostTip($user)): ?>
                            <form method="POST" action="/public/msg/tip_post.php" class="msg-form">
                                <input type="text" name="title" class="bc-input" placeholder="Tip title (optional)">
                                <textarea name="message" class="bc-input msg-textarea" required></textarea>

                                <button class="bc-btn msg-board-btn">
                                    <i class="fa-solid fa-lightbulb"></i> Post Tip
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="msg-locked">
                                Tips are restricted to officers and above.
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                    <!-- ======================
                         QUESTION INPUT
                    ======================= -->
                        <?php if ($tab === 'questions'): ?>

                            <?php if (canAskQuestion($user)): ?>
                                <form method="POST" action="/public/msg/question_post.php" class="msg-form">

                                    <textarea name="message"
                                            class="bc-input msg-textarea"
                                            required
                                            placeholder="Ask your question..."></textarea>

                                    <button class="bc-btn msg-board-btn">
                                        <i class="fa-solid fa-question"></i> Ask Question
                                    </button>

                                </form>
                            <?php endif; ?>

                        <?php endif; ?>

                    <hr>

                    <!-- ======================
                         FEED (UNIFIED LOOP)
                    ======================= -->
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

                                <?php if ($p['type'] === 'tip'): ?>

                                    <?php
                                        $avg = ($p['rating_count'] > 0)
                                            ? ($p['rating_sum'] / $p['rating_count'])
                                            : 0;
                                    ?>

                                    <div class="msg-rating">
                                        ⭐ <?= number_format($avg, 1) ?>
                                        (<?= (int)$p['rating_count'] ?>)
                                    </div>

                                <?php else: ?>

                                    <div class="msg-votes">
                                        👍 <?= (int)$p['votes'] ?>
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