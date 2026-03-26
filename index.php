<?php
// ==============================
// BOOTSTRAP / CONFIG / HELPERS
// ==============================
require_once __DIR__ . '/core/bootstrap.php';      // sessions, environment
require_once __DIR__ . '/config/config.php';      // BASE_URL, DB
require_once __DIR__ . '/helpers/functions.php';  // e(), isLoggedIn(), hasRole(), fetchAll()

// ==============================
// SERVICES & CONTROLLERS
// ==============================
require_once __DIR__ . '/services/MonsterHuntService.php';
require_once __DIR__ . '/controllers/MonsterHuntController.php';

// ==============================
// CONTROLLER DATA
// ==============================
$data = monsterHuntController($pdo);   // returns array of $squads, $creatures, $attackGroups etc.
extract($data);

// ==============================
// PAGE SETTINGS
// ==============================
$pageClass = 'page-index';

// ==============================
// HEADER
// ==============================
require_once __DIR__ . '/includes/header.php';

/* page protected
requireLogin(); 
  if(hasRole('veteran')){
      // show veteran+ content
  }
*/

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
              <div class="bc-content-leader" style="text-align:center">
                <h2>Command the Hunt</h2>
                <h2>Control the Outcome.</h2>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">              
                <p>
                    <div style="padding-bottom: 10px;">For players who don’t guess -> they calculate.</div>
                    <div style="padding-bottom: 10px;">Every Epic, Squad, Citiadel hunt -> matters.</div>
                    <div style="padding-bottom: 10px;">Scattered data points -> repeatable wins.</div>
                </p>
                <!--
                <p>
                    Plan smarter attacks using real matchup logic, creature bonuses, and survivability math. 
                    Instead of over-sending or guessing losses, you’ll know exactly what to deploy — and why it works.
                </p>

                <p>
                    Whether you’re optimizing monster hunts, testing formations, or scaling your efficiency, 
                    this is your command layer above the game.
                </p>

                <ul>
                    <li>Creature vs monster optimization</li>
                    <li>Attack formation builder</li>
                    <li>Loss + efficiency calculations</li>
                    <li>Scalable strategy tools (in progress)</li>
                </ul>
                -->
              </div>
            </div>
          </div>

          <div class="bc-card"> 
            <a href="<?= BASE_URL ?>/monster_hunt.php" class="bc-card">
              
              <div class="bc-img" style="height: 220px;">
                <img src="<?= BASE_URL ?>/images/cards/war_table.jpg" alt="Battle Council">
              </div>    

              <div class="bc-content">
                <div class="bc-content-leader" style="text-align:center">
                  <h2>Monster Hunt</h2>
                  <h2>Calculator</h2>
                </div>

                <div class="bc-content-inner" style="padding: 15px;">  
                  <p>
                    This is NOT a regular stacking calculator. This is for efficiency
                    of hunting monster squads on the world map.
                  </p>
                </div>
              </div>

            </a>
          </div>

          <div class="bc-card">
            <div class="bc-img" style="height: 220px;">
              <img src="<?= BASE_URL ?>/images/cards/tavern.jpg" alt="tavern">
            </div>    
            <div class="bc-content">
              <div class="bc-content-leader" style="text-align:center">
                <h2 class="future-header">Player Tavern</h2>
                <h3 class="future">Future Use</h3>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">  
                <p>Come in! Grab An ale and swap lies about your conquests</p>
              </div>
            </div>
          </div>

          <div class="bc-card">
            <div class="bc-img" style="height: 220px;">
              <img src="<?= BASE_URL ?>/images/cards/legion_forge.jpg" alt="legion-forge">
            </div>    
            <div class="bc-content">
              <div class="bc-content-leader" style="text-align:center">
                <h2 class="future-header">Clan & Kingdom</h2>
                <h3 class="future">Future Use</h3>
              </div>
              <div class="bc-content-inner" style="padding: 15px;">  
                <p>looking for details on another realm...</p>
              </div>
            </div>
          </div>

          <div class="bc-card">
            <div class="bc-img" style="height: 220px;">
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
            <div class="bc-img" style="height: 220px;">
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