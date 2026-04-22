<?php
/* =========================
   FOOTER FLAGS (fallback safe)
========================= */
$requiresApp = $requiresApp ?? true;
?>

    <div class="container bc-page">
        <div class="bc-row">
            <div class="bc-col-12">
                <div class="bc-card">
                  <footer class="footer">

                      <div class="footer-inner">

                          <div class="footer-left">
                              &copy; <?= date('Y') ?> BattleCouncil.com - v2.19.3 -- kinda beta
                          </div>

                          <div class="footer-right">
                              <a href="<?= BASE_URL ?>/public/about.php">About</a>
                              <span class="footer-sep">|</span>

                              <a href="<?= BASE_URL ?>/public/faq.php">FAQ</a>
                              <span class="footer-sep">|</span>

                              <a href="<?= BASE_URL ?>/public/privacy.php">Privacy</a>
                              <span class="footer-sep">|</span>

                              <a href="<?= BASE_URL ?>/public/legal.php">Legal</a>
                          </div>

                      </div>

                  </footer>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>