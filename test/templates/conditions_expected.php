<!DOCTYPE html>
<html>
  <body>
    <div>
      <?php if (1 + 1 == 2): ?>
        <p>Hello world</p>
      <?php endif; ?>
      <?php if (1 + 1 == 2): ?>
        <p>Hello</p>
      <?php else: ?>
        <p>World</p>
      <?php endif; ?>
      <?php if (1 + 1 == 2): ?>
        <p>Hello world</p>
      <?php elseif (1 + 1 == 3): ?>
        <p>This is not possible</p>
      <?php else: ?>
        <p>Omg</p>
      <?php endif; ?>
      <?php if (1 + 1 == 3): ?>
        <p>This is not possible</p>
      <?php elseif (1 + 1 == 2): ?>
        <p>Hello world</p>
      <?php endif; ?>
    </div>
  </body>
</html>
