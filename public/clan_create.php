<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

require_once __DIR__ . '/../services/PointsService.php';
require_once __DIR__ . '/../services/ClanServices.php';

// ==============================
// PAGE SETTINGS
// ==============================
$pageTitle = "Clan Intelligence System";
$pageCss   = "clanCreate";

// ==============================
// DATA
// ==============================
$userId = 1;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = ClanServices::submit($pdo, $userId, $_POST);
}

$balance = PointsService::getBalance($pdo, $userId);
$ledger  = PointsService::getLedger($pdo, $userId);

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="container-lead">
        Clan Intelligence System
    </div>


    <div class="bc-grid">

        <!-- BALANCE -->
        <div class="bc-card">
            <div class="bc-content">
                <div class="bc-content-leader">
                    <h2>Balance</h2>
                </div>
                <div class="bc-content-inner">
                    <p><?= $balance ?> Points</p>
                </div>
                <div>
                    <input type="text" id="kingdomSearch" placeholder="Search Kingdom..." autocomplete="off">
                    <input type="hidden" name="kingdom" id="kingdomID">
                    <div id="kingdomResults" class="search-results"></div>
                </div>
            </div>
        </div>

        <!-- MESSAGE -->
        <?php if ($message): ?>
        <div class="bc-card">
            <div class="bc-content">
                <div class="bc-content-leader">
                    <h2><?= $message['success'] ? 'SUCCESS' : 'ERROR' ?></h2>
                </div>
                <div class="bc-content-inner">
                    <p>
                        <?= $message['success']
                            ? "Points Awarded: " . $message['points_awarded']
                            : $message['error'] ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- FORM -->
        <div class="bc-card">
            <div class="bc-content">
                <div class="bc-content-leader">
                    <h2>Clan Submission</h2>
                </div>

                <div class="bc-content-inner">

                    <form method="POST" class="bc-form">

                        <label>Kingdom K</label>
                        <input name="k" required>

                        <label>Capital X</label>
                        <input name="x" required>

                        <label>Capital Y</label>
                        <input name="y" required>

                        <label>Clan Name</label>
                        <input name="clan_name" required>

                        <label>Clan Abbrev.</label>
                        <input name="clan_abbr" maxlength="3" required>

                        <label>Clan Leader</label>
                        <input name="leader">

                        <label>Clan Capital Level</label>
                        <input name="level"> 
                      
                        <label>Language</label>
                        <select id="languageSelect" name="language">
                            <option value="">Select Language</option>
                            <option value="other">+ Add New</option>
                        </select>

                        <label>Has ROE</label>
                        <select name="roe">
                            <option value="">--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>



                        <input type="text" id="languageNew" placeholder="Enter new language" style="display:none;">

                        <button type="submit">Submit Clan</button>

                    </form>

                </div>
            </div>
        </div>

        <!-- LEDGER -->
        <div class="bc-card">
            <div class="bc-content">
                <div class="bc-content-leader">
                    <h2>Recent Activity</h2>
                </div>
                <div class="bc-content-inner">
                    <?php foreach ($ledger as $row): ?>
                        <div class="ledger-row">
                            <?= $row['points'] ?> | <?= $row['reason'] ?> | <?= $row['created_at'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>