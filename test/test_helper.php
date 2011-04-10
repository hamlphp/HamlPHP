<?php

require_once 'src/HamlPHP/HamlPHP.php';
require_once 'src/HamlPHP/Storage/FileStorage.php';
require_once 'src/HamlPHP/Compiler.php';

define("HAMLPHP_DIR_SRC", dirname(__FILE__) . '/../src/HamlPHP');

function template($template) {
  return dirname(__FILE__) . '/templates/' . $template;
}

function contents($template) {
  return file_get_contents(template($template));
}

function getTestCompiler() {
  $hamlPHP = new HamlPHP(new FileStorage(dirname(__FILE__) . '/tmp/'));
  $hamlPHP->disableCache();
  return $hamlPHP->getCompiler();
}