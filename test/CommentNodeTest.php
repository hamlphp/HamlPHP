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
    $this->compiler = new Compiler();
  }

  public function testComments()
  {
    $html = $this->compiler->parseFile(template('comments.haml'));
    $this->assertEquals(contents('comments_expected.html'), $html);
  }
}
