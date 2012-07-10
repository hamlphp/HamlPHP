<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      body { color: #000; }
      
      p {
        color: #fff;
      }
      
      #container { color: #fff; }
    </style>
    %p should not render
    / neither this
    
    <script type="text/javascript">
      $(function() {
        function helloWorld() {
          alert('Hello world');
        }
      });
    </script>
    <?php
    $variable = 1;
    if(!function_exists('helloWorld'))
    {
	    function helloWorld() {
    	  return 'Hello world';
    	}
    }
    ?>
  </head>
  <body>
    <p>test</p>
    <p>
      %p testing plain
        nesting
          nesting
          = 2 + 2
    </p>
  </body>
</html>