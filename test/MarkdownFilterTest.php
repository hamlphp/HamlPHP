<?php

require_once 'BaseTestCase.php';

class MarkdownFilterTest extends BaseTestCase
{
	/**
	 * @test
	 */
	public function can_render_markdown()
	{
		$actual = $this->compiler->parseFile($this->getTemplatePath('markdown'));

		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($this->getExpectedResult('markdown'));
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function can_render_markdown_extra()
	{
		$actual = $this->compiler->parseFile($this->getTemplatePath('markdown-extra'));
		
		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($this->getExpectedResult('markdown-extra'));
		
		$this->compareXmlStrings($expected, $actual);
	}
}