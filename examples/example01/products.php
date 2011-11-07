<?php
  require_once('../../src/HamlPHP/HamlPHP.php');
  require_once('../../src/HamlPHP/Storage/FileStorage.php');
  
  $parser = new HamlPHP(new FileStorage(dirname(__FILE__) . '/tmp/'));

  $dataArray = array(
    'page_title' => 'Product Selection',
    'title' => 'Products',
    'footer' => '', // Declare all the content_for entries
  );
  
  $dataArray['article'] = $parser->parseFile('app/views/products.haml', $dataArray);
  
  $content = $parser->parseFile('app/views/layout.haml', $dataArray);

  echo $content;
/* */