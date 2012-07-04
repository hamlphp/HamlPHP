<?php

require_once 'test_helper.php';

class CommentNodeTest extends PHPUnit_Framework_TestCase
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

  public function testComments()
  {
    $html = $this->compiler->parseFile(template_path('comments'));
    $this->assertEquals(expected_result('comments'), $html);
  }
}
