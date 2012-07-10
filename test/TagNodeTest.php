<?php

require_once 'BaseTestCase.php';

class TagNodeTest extends BaseTestCase
{
  public function testForLoop()
  {
    $actual = $this->compiler->parseFile( $this->getTemplatePath('forloop'));
   	$expected = $this->getExpectedResult('forloop');

    $actual = $this->evaluator->evaluate($actual);
    $expected = $this->evaluator->evaluate($expected);
    
    $this->compareXmlStrings($expected, $actual);
  }

  public function testConditions()
  {
    $actual = $this->compiler->parseFile( $this->getTemplatePath('conditions'));
    $expected = $this->getExpectedResult('conditions');
	
    $actual = $this->evaluator->evaluate($actual);
    $expected = $this->evaluator->evaluate($expected);
    
    $this->compareXmlStrings($expected, $actual);
  }
}
