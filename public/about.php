<?php
$pageTitle = "About";
$pageClass = "page-static";

// 🔥 KEY FLAGS
$requiresAuth = false;
$loadAppJS    = false;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bc-container">

    <div class="bc-row">
        <div class="bc-col-12">

            <div class="bc-card">
                <div class="bc-card-header">
                    <h2>About</h2>
                </div>

                <div class="bc-card-body">
                    <p>Your content here...</p>
                </div>
            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>