<?php

require_once 'BaseTestCase.php';
require_once HAMLPHP_ROOT . 'Lang/Helpers.php';

class HelpersTest extends BaseTestCase
{
  public function testAttributeFunction()
  {
  	ob_start();
  	atts(array(array('dir' => 'ltr', 'lang' => 'pt_BR')));
  	$actual = trim(ob_get_clean());

  	$expected = 'dir="ltr" lang="pt_BR"';

  	$this->assertEquals(
  		$expected, $actual, "Failed for a single attribute function. Expected: $expected. Got: $actual"
  	);
  }
}