
  </main><!-- /.admin-content -->

  <!-- ═══ FOOTER ADMIN ═══ -->
  <footer class="admin-footer">
    <p>&copy; <?= date('Y') ?> Semillitas del Futuro — Panel de Administración</p>
  </footer>

  <?php
  $siteBase = $siteBase ?? '../';
  ?>
  <script src="<?= $siteBase ?>assets/js/main.js"></script>
  <script>
    // Toggle del menú admin en mobile
    document.addEventListener('DOMContentLoaded', () => {
      const toggle = document.querySelector('.admin-nav__toggle');
      const menu   = document.querySelector('.admin-nav__menu');
      if (toggle && menu) {
        toggle.addEventListener('click', () => {
          const open = menu.classList.toggle('active');
          toggle.setAttribute('aria-expanded', open);
        });
      }
    });
  </script>

  <?php if (!empty($adminExtraJS)): ?>
    <?php foreach ($adminExtraJS as $js): ?>
    <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>
