<?php

require_once 'test_helper.php';

class FilterTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = getTestCompiler();
  }

  public function testAttributes()
  {
    $actual = $this->compiler->parseFile(template_path('filters'));
    $this->assertEquals(expected_result('filters'), $actual);
  }
}
