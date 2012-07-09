<?php

require_once 'BaseTestCase.php';

class CommentNodeTest extends BaseTestCase
{
  public function testComments()
  {
    $html = $this->compiler->parseFile( $this->getTemplatePath('comments'));
    $this->assertEquals( $this->getExpectedResult('comments'), $html);
  }
}
