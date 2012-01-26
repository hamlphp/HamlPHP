<?php
  require_once('../../src/HamlPHP/HamlPHP.php');
  require_once('../../src/HamlPHP/Storage/FileStorage.php');
  
  $parser = new HamlPHP(new FileStorage(dirname(__FILE__) . '/tmp/'));

  $dataArray = array(
    'page_title' => 'Home',
    'title' => 'Welcome!',
    'footer' => '', // Declare all the content_for entries
  );
  
  $dataArray['article'] = $parser->parseFile('app/views/home.haml', $dataArray);
  
  $content = $parser->parseFile('app/views/layout.haml', $dataArray);

  echo $content;