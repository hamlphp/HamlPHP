<?php

require_once 'test_helper.php';

class TryHamlTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = new Compiler();
  }

  public function testForLoop()
  {
    $actual = $this->compiler->parseFile(template('try.haml'));
    $this->assertEquals(contents('try_expected.html'), $actual);
  }
}
