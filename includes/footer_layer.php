<footer class="footer">
  <p>&copy; <?= date('Y') ?> BattleCouncil.com - v2.0 - highly beta</p>
</footer>

  <script>
    const bonusMatrix = <?= json_encode($bonusMatrix ?? []) ?>;
  </script>

  <script src="<?= BASE_URL ?>/../assets/js/LayerEngine.js"></script>  
  <script src="<?= BASE_URL ?>/../assets/js/layer.js"></script>

</body>
</html>