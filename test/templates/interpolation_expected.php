<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      p { color: <?php echo 'black'; ?>; }
    </style>
  </head>
  <body>
    <div id="container">
      <p>
        this line has interpolation <?php echo 'hello world'; ?>.
      </p>
      <p><?php echo 'hello world'; ?></p>
    </div>
  </body>
</html>
