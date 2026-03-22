<?php 

require_once __DIR__ . '/config/config.php'; 
include __DIR__ . '/includes/header.php'; 

/* page protected
requireLogin(); 
  if(hasRole('veteran')){
      // show veteran+ content
  }
*/

  ?>
<main class="container">
  <div class="grid">
    
    <div class="card">
      <?php include __DIR__ . '/components/cards/welcome.php'; ?>
    </div>

    <div class="card">
      <h3>Another Card</h3>
      <p>More content</p>
    </div>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>