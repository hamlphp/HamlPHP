<?php

require_once 'src/HamlPHP/Config.php';
require_once 'src/HamlPHP/HamlPHP.php';
require_once 'src/HamlPHP/Storage/FileStorage.php';
require_once 'src/HamlPHP/Compiler.php';

if(!defined('TEST_TMP_DIR'))
	define('TEST_TMP_DIR', dirname(__FILE__) . '/tmp/');

function template_path($template) {
	return dirname(__FILE__) . '/templates/' . $template . '.haml';
}

function expected_result($template) {
	return file_get_contents(dirname(__FILE__) . '/templates/' . $template . '_expected.php');
}

function getTestCompiler() {
	$hamlPHP = new HamlPHP(new FileStorage(dirname(__FILE__) . '/tmp/'));
	$hamlPHP->disableCache();
	return $hamlPHP->getCompiler();
}