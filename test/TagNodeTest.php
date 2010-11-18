<?php

require_once 'test_helper.php';

class TagNodeTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = new Compiler();
  }

  public function testForLoop()
  {
    $actual = $this->compiler->parseFile(template('forloop.haml'));
    $this->assertEquals(contents('forloop_expected.html'), $actual);
  }
}
