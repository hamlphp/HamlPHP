<?php

require_once 'BaseTestCase.php';
require_once HAMLPHP_ROOT . 'ContentEvaluator/ContentEvaluator.php';

/**
 * test case.
 */
class HtmlStyleAttributesTest extends BaseTestCase
{
	/**
	 * @test
	 * 
	 * HTML-style attributes can be stretched across multiple lines just like hash-style attributes
	 * 
	 * %script(type="text/javascript"
	 *         src="javascripts/script_#{2 + 7}")
	 */
	public function Can_be_stretched_over_multiple_lines()
	{
		$actual = trim($this->compiler->parseLines(array(
			'%script(type="text/javascript"',
			'		src = "javascripts/script_#{2 + 7}")/'
		)));
		
		$expected = '<script type="text/javascript" src="javascripts/script_<?php echo 2 + 7; ?>" />';
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @test
	 * 
	 * Variables can be used by omitting the quotes
	 * 
	 * %a(title=$title href=$href) Stuff 
	 */
	public function Variables_can_be_used_by_omitting_the_quotes()
	{
		$actual = trim($this->compiler->parseString(
			'%a(title=$title href=$href) Stuff'));
		$expected = '<a <?php atts(array(\'title\' => $title, \'href\' => $href)); ?>>Stuff</a>';
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @test
	 *     
	 * HTML-style boolean attributes can be written just like HTML:
	 *     
	 * %input(selected)
	 */
	public function boolean_attributes_can_be_written_just_like_HTML()
	{
		$actual = trim($this->compiler->parseString(
			'%input(selected)'));
	
		$expected = '<input selected="selected"></input>';
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @test
	 * @expectedException SyntaxErrorException
	 * 
	 * More complicated expressions arenÕt allowed. For those youÕll have to use the {} syntax. 
	 * You can, however, use both syntaxes together.
	 * 
	 * %a(title=$title){href => $link->href} Stuff
	 */
	public function More_complicated_expressions_are_not_allowed()
	{
		$actual = trim($this->compiler->parseString(
			'%a(title=$title href => $link->href) Stuff'));
		$expected = '<a <?php atts(array(\'title\' => $title, \'href\' => $link->href)); ?>>Stuff</a>';
		
		// shouldn't get here
		$this->fail("SyntaxErrorException wasn't thrown");
	}

	/**
	 * @test
	 * 
	 * More complicated expressions arenÕt allowed. For those youÕll have to use the {} syntax. 
	 * You can, however, use both syntaxes together.
	 * 
	 * %a(title=$title){href => $link->href} Stuff
	 */
	public function Can_be_combined_with_hash()
	{
		$actual = trim($this->compiler->parseString(
			'%a(title=$title){href => $link->href} Stuff'));
		$expected = '<a <?php atts(array(\'title\' => $title, \'href\' => $link->href)); ?>>Stuff</a>';
	
		$this->assertEquals($expected, $actual);
	}
}

