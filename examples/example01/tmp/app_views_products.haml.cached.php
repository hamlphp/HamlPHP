<ul class="products">
  <li><?php echo 1 * 6 ?></li>
  <?php for ($i = 0; $i < 10; $i++): ?>
    <li><?php echo $i + 3 ?></li>
  <?php endfor; ?>
</ul>
<?php $footer = <<< EOT
  <p>
    Products page footer
  </p>

EOT
?>
