<?php

require_once 'src/HamlPHP/Compiler.php';

function template($template) {
  return dirname(__FILE__) . '/templates/' . $template;
}

function contents($template) {
  return file_get_contents(template($template));
}
