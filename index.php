<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/helpers/auth.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

// ==============================
// PAGE SETTINGS
// ==============================
$pageClass = 'page-index';

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/includes/header.php';

/* page protected */
requireLogin(); 
  if(hasRole('veteran')){
      // show veteran+ content
  }


  ?>

    <div class="container">
        <div class="bc-grid">
          
          <div class="bc-card">
            <div class="bc-img" style="height: 220px;">
              <video src="/../images/trent/Trent_the_Elder_generated.mp4" 
                alt="Battle Council Video"
                controls
                autoplay
                muted
                loop
                style="border-radius:8px;">
                Your browser does not support the video tag.
              </video>
            </div>    
              <div class="bc-content">
                <a href="<?= BASE_URL ?>/public/pricing.php" class="bc-card">
                <div class="bc-content-leader" style="text-align:center">
                  <h2>Command the Hunt</h2>
                  <h2>Control the Outcome.</h2>
                </div>
                <div class="bc-content-inner" style="padding: 15px;">              
                  <p>
                      <div style="padding-bottom: 10px;">For players who don’t guess -> they calculate.</div>
                      <div style="padding-bottom: 10px;">Every Epic, Squad, Citiadel hunt -> matters.</div>
                      <div style="padding-bottom: 10px;">Focused data points -> repeatable wins.</div>
                  </p>

                </div>
              </a>
            </div>
          </div>

          <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/public/calculators.php" class="bc-card">
              
              <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/war_table.jpg" alt="Battle Council">
              </div>    

              <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                  <h2>Monster Hunting</h2>
                  <h2>Calculators</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                  <div class="lead-section">
                    <p class="lead-warning">
                      These are NOT regular stacking calculators.
                    </p>

                    <div class="lead-list">
                      <div class="lead-item">
                        <span class="icon">👹</span>
                        <span>Use Creatures to Hunt Calculator</span>
                      </div>

                      <div class="lead-item">
                        <span class="icon">📊</span>
                        <span>Advanced Layered Calculator</span>
                      </div>

                      <div class="lead-item">
                        <span class="icon">🏰</span>
                        <span>Citadel Cracker Calculator</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </a>
          </div>

          <div class="bc-card">
            <a href="<?= BASE_URL ?>/public/tavern.php" class="bc-card">
              <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/tavern.jpg" alt="tavern">
              </div>    
              <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                  <h2 >Player Tavern</h2>
                  <h2> -- OPEN -- </h2>
                </div>
                <div class="bc-content-inner" style="padding: 15px;">  
                  <p>Come in! Grab An ale and swap lies about your conquests
                  <br /><br />
                  <small>Creature Karaoke is now on Tues</small>
                  </p>
                </div>
              </div>
            </a>
          </div>

          <div class="bc-card">
            <a href="<?= BASE_URL ?>/public/kingdom.php" class="bc-card">
            <div class="bc-img" style="height: 220px;">
              <img src="<?= BASE_URL ?>/images/cards/kingdom.png" alt="The Kingdom">
            </div>    
            <div class="bc-content">
              <div class="bc-content-leader" style="text-align:center">
                <h2 >Clans & Kingdom</h2>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">  
                <p>looking for details on another realm...</p>
              </div>
            </div>
            </a>
          </div>

          <div class="bc-card">
            <div class="bc-img-soon" style="height: 220px;">
              <img src="<?= BASE_URL ?>/images/cards/beast_codex.jpg" alt="Beast Codex">
            </div>    
            <div class="bc-content">
              <div class="bc-content-leader" style="text-align:center">
                <h2 class="future-header">Beast Codex</h2>
                <h3 class="future">Future Use</h3>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">  
                <p>Get Monster and Creature details</p>
              </div>
            </div>
          </div>

          <div class="bc-card">
            <div class="bc-img-soon" style="height: 220px;">
              <img src="<?= BASE_URL ?>/images/cards/captain_archive.jpg" alt="Player Archive">
            </div>    
            <div class="bc-content">
              <div class="bc-content-leader" style="text-align:center">
                <h2 class="future-header">Player Bounties</h2>
                <h3 class="future">Future Use</h3>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">  
                <p>Gotta Grudge? Maybe you need a bounty on someone</p>
              </div>
            </div>
          </div>
      </div>
    </div>
      <row style="height:30px;" />
  </main>

<script>
window.attackGroups = <?= json_encode($attackGroups ?? [], JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php
// ==============================
// FOOTER
// ==============================
require_once __DIR__ . '/includes/footer.php';