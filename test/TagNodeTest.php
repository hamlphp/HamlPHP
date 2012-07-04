<?php

require_once 'test_helper.php';

class TagNodeTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = getTestCompiler();
  }

  public function testForLoop()
  {
    $actual = $this->compiler->parseFile(template_path('forloop'));
    $this->assertEquals(expected_result('forloop'), $actual);
  }

  public function testConditions()
  {
    $actual = $this->compiler->parseFile(template_path('conditions'));
    $this->assertEquals(expected_result('conditions'), $actual);
  }
}
