<?php

require_once 'test_helper.php';

class AttributesTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = new Compiler();
  }

  public function testAttributes()
  {
    $actual = $this->compiler->parseFile(template('filters.haml'));
    $this->assertEquals(contents('filters_expected.html'), $actual);
  }
}
