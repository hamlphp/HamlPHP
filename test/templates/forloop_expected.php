<!DOCTYPE html>
<html>
  <body>
    <div id="container">
      <ul class="numbers">
        <?php for ($i = 0; $i < 5; ++$i): ?>
          <li><?php echo $i + 1 ?></li>
        <?php endfor; ?>
      </ul>
      <ul class="colon">
        <?php for($i = 0; $i < 2; $i++): ?>
          <li><?php echo $i ?></li>
        <?php endfor; ?>
      </ul>
    </div>
  </body>
</html>
