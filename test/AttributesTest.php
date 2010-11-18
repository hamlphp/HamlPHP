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
    $actual = $this->compiler->parseFile(template('attributes.haml'));
    $this->assertEquals(contents('attributes_expected.html'), $actual);
  }
}
