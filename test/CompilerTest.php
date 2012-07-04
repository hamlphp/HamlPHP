<?php

require_once 'test_helper.php';

class CompilerTest extends PHPUnit_Framework_TestCase
{
  /**
   * Instance of a compiler.
   *
   * @var Compiler
   */
  protected $compiler = null;

  public function setUp()
  {
    $this->compiler = getTestCompiler();
  }

  public function testCompilingFromString()
  {
    $content = "%html\n  %p Hello world";
    $html = $this->compiler->parseString($content);
    $expected = "<html>\n  <p>Hello world</p>\n</html>\n";
    $this->assertEquals($expected, $html);
  }

  public function testInlinePhp()
  {
  	$html = $this->compiler->parseFile(template_path('inlinephp'));
  	$this->assertEquals(expected_result('inlinephp'), $html);
  }
  
  public function testCompilingFromFile()
  {
    $html = $this->compiler->parseFile(template_path('test'));
    $this->assertEquals(expected_result('test'), $html);
  }
}
