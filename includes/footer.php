  <!-- ═══ FOOTER ═══ -->
  <footer class="footer">
    <div style="max-width: 1200px; margin: 0 auto;">
      <p class="footer__text">
        &copy; <?= date('Y') ?> Semillitas del Futuro &mdash; Plataforma Educativa Primaria
      </p>
      <div class="footer__credits">
        <span>Compromiso con la educación ambiental y científica</span>
      </div>
    </div>
  </footer>

  <!-- JS Principal -->
  <script src="<?= $basePath ?? '' ?>assets/js/main.js"></script>

  <?php if (!empty($extraJS)): ?>
    <?php foreach ($extraJS as $js): ?>
    <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>
