<?php

require_once 'test_helper.php';

class TryHamlTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = getTestCompiler();
  }

  public function testForLoop()
  {
    $actual = $this->compiler->parseFile(template_path('try'));
    $this->assertEquals(expected_result('try'), $actual);
  }
}
