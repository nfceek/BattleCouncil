<?php
/* =========================
   FOOTER FLAGS (fallback safe)
========================= */
$requiresApp = $requiresApp ?? true;
?>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> BattleCouncil.com - v2.0 - highly beta</p>
</footer>

<?php if ($requiresApp): ?>
    <!-- CORE APP JS (if not already loaded in header) -->
    <script src="/assets/js/app.js"></script>
<?php endif; ?>

</body>
</html>