<?php

require_once 'BaseTestCase.php';

class CompilerTest extends BaseTestCase
{
  public function testCompilingFromString()
  {
    $content = "%html\n  %p Hello world";
    $html = $this->compiler->parseString($content);
    $expected = "<html>\n  <p>Hello world</p>\n</html>\n";
    $this->assertEquals($expected, $html);
  }

  public function testInlinePhp()
  {
  	$actual = $this->compiler->parseFile( $this->getTemplatePath('inlinephp'));
  	$expected = $this->getExpectedResult('inlinephp');
  	
  	$actual = $this->evaluator->evaluate($actual);
  	$expected = $this->evaluator->evaluate($expected);
  	
  	$this->compareXmlStrings($expected, $actual);
  }
  
  public function testCompilingFromFile()
  {
    $html = $this->compiler->parseFile( $this->getTemplatePath('test'));
    $this->assertEquals( $this->getExpectedResult('test'), $html);
  }
}
