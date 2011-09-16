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
    $actual = $this->compiler->parseFile(template('forloop.haml'));
    $this->assertEquals(contents('forloop_expected.html'), $actual);
  }

  public function testConditions()
  {
    $actual = $this->compiler->parseFile(template('conditions.haml'));
    $this->assertEquals(contents('conditions_expected.html'), $actual);
  }
}
