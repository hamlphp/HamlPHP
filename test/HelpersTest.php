<?php

require_once '../src/HamlPHP/Helpers.php';
require_once 'test_helper.php';

class HelpersTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = new Compiler();
  }

  public function testAttributeFunction()
  {
  	ob_start();
  	atts(array(array('dir' => 'ltr', 'lang' => 'pt_BR')));
  	$actual = ob_get_clean();
  	ob_end_clean();
  	$expected = ' dir="ltr" lang="pt_BR"';
  	
  	$this->assertEquals(
  		$expected, $actual, "Failed for a single attribute function. Expected: $expected. Got: $actual"
  	); 
  }
}