<?php

require_once 'BaseTestCase.php';

class TryHamlTest extends BaseTestCase
{
  public function testForLoop()
  {
    $actual = $this->compiler->parseFile( $this->getTemplatePath('try'));
    $expected = $this->getExpectedResult('try');

    $actual = $this->evaluator->evaluate($actual);
    $expected = $this->evaluator->evaluate($expected);
    
    $this->compareXmlStrings($expected, $actual);
  }
}
