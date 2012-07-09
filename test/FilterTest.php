<?php

require_once 'BaseTestCase.php';

class FilterTest extends BaseTestCase
{
  public function testAttributes()
  {
    $actual = $this->compiler->parseFile( $this->getTemplatePath('filters'));
    $this->assertEquals( $this->getExpectedResult('filters'), $actual);
  }
}
