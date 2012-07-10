<?php

require_once 'BaseTestCase.php';

class FilterTest extends BaseTestCase
{
  public function testAttributes()
  {
    $actual = $this->compiler->parseFile($this->getTemplatePath('filters'));
	$expected = $this->getExpectedResult('filters');
	
	$actual = $this->evaluator->evaluate($actual);
	$expected = $this->evaluator->evaluate($expected);

	$this->compareXmlStrings($expected, $actual);
  }
}
