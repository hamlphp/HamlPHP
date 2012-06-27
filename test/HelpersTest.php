<?php

require_once 'test_helper.php';
require_once HAMLPHP_ROOT . 'Lang/Helpers.php';

class HelpersTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = getTestCompiler();
  }

  public function testAttributeFunction()
  {
  	ob_start();
  	atts(array(array('dir' => 'ltr', 'lang' => 'pt_BR')));
  	$actual = ob_get_clean();

  	$expected = ' dir="ltr" lang="pt_BR"';

  	$this->assertEquals(
  		$expected, $actual, "Failed for a single attribute function. Expected: $expected. Got: $actual"
  	);
  }
}