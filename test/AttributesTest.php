<?php

require_once 'BaseTestCase.php';

class AttributesTest extends BaseTestCase
{
	public function testAttributeFunction()
	{
		// test 1
		$actual = trim($this->compiler->parseString('%p{p_atts()}'));
		$expected = '<p <?php atts(array(p_atts())); ?>></p>';
		
		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);

		$this->compareXmlStrings($expected, $actual);

		// test 2
		$actual = trim($this->compiler->parseString('.user{user_atts($userId)}'));
		$expected = "<div <?php atts(array('class' => 'user', user_atts(\$userId))); ?>></div>";
		
		$actual = $this->evaluator->evaluate($actual, array('userId' => 123));
		$expected = $this->evaluator->evaluate($expected, array('userId' => 123));

		$this->compareXmlStrings($expected, $actual);

		// test 3
		$actual = trim($this->compiler->parseString('.user.teatcher{user_atts($userId), teatcher_atts($userId, $viewerId)}'));
		$expected = "<div <?php atts(array('class' => 'user teatcher', user_atts(\$userId), teatcher_atts(\$userId, \$viewerId))); ?>></div>";
		
		$actual = $this->evaluator->evaluate($actual, array('userId' => 123, 'viewerId' => 456));
		$expected = $this->evaluator->evaluate($expected, array('userId' => 123, 'viewerId' => 456));

		$this->compareXmlStrings($expected, $actual);
	}

	public function testAttributes()
	{
		$actual = $this->compiler->parseFile($this->getTemplatePath('attributes'));
		$expected = $this->getExpectedResult('attributes');

		$actual = $this->evaluator->evaluate($actual, array('item_here' => 'item here', 'complex' => 'complex', 'multiline' => 'multiline'));
		$expected = $this->evaluator->evaluate($expected, array('item_here' => 'item here', 'complex' => 'complex', 'multiline' => 'multiline'));
		
		$this->compareXmlStrings($expected, $actual);
	}

	public function testHtmlAttributes()
	{
		$el = new Element('%div(data-url = \'aasd\')', $this->compiler);
		$this->assertEquals(array(
			'data-url' => array(
				't' => 'str',
				'v' => "'aasd'"
			)
		), $el->getAttributes(), 'Failed for 1 attribute using single quotes.');
		
		$el = new Element('%div(class="paragraph" style="background: #fff; color: #000")', $this->compiler);
		$this->assertEquals(array(
			'class' => array(
				array(
					't' => 'str',
					'v' => 'paragraph'
				)
			),
			'style' => array(
				't' => 'str',
				'v' => '"background: #fff; color: #000"'
			)
		), $el->getAttributes(), 'Failed for two attributes using double quotes');
		
		$el = new Element('%div(class="paragraph" style=$css value="a value")', $this->compiler);
		$this->assertEquals(array(
			'class' => array(
				array(
					't' => 'str',
					'v' => 'paragraph'
				)
			),
			'style' => array(
				't' => 'php',
				'v' => '$css'
			),
			'value' => array(
				't' => 'str',
				'v' => '"a value"'
			)
		), $el->getAttributes(), 'Failed for 3 attributes with the middle one containning PHP code');
		
		$exception_trown = false;
		try
		{
			$el = new Element('%div(data-url = \'aasd\'', $this->compiler);
		}
		catch(SyntaxErrorException $e)
		{
			$exception_trown = true;
		}
		$this->assertTrue($exception_trown, 'Failed detecting missing ) for single line list');
		
		// multiline
		$html = trim($this->compiler->parseLines(array(
			"%div(class='paragraph' \n",
			" style=\$css\n",
			" value=\"a value\")"
		)));
		
		$this->assertEquals('<div <?php atts(array(\'class\' => \'paragraph\', \'style\' => $css, \'value\' => "a value")); ?>></div>', $html, 'Failed for multiline attributes with 3 attributes and the middle one containning PHP code');
		
		$exception_trown = false;
		try
		{
			$this->compiler->parseLines(array(
				"%div(class='paragraph' \n",
				" style=\$css\n",
				" value=\"a value\""
			));
		}
		catch(SyntaxErrorException $e)
		{
			$exception_trown = true;
		}
		$this->assertTrue($exception_trown, 'Failed detecting missing ) for multiline list');
	}

	public function testHashAttributes()
	{
		$el = new Element('%div{class=>"vacuum", data-url => \'aasd\'}', $this->compiler);
		$this->assertEquals(array(
			'class' => array(
				array(
					't' => 'str',
					'v' => 'vacuum'
				)
			),
			'data-url' => array(
				't' => 'str',
				'v' => "'aasd'"
			)
		), $el->getAttributes(), 'Failed for 1 attribute using single quotes.');
		
		// multiline
		$html = trim($this->compiler->parseLines(array(
			"%p{:id =>['this', \$item_here], ",
			"      has => \$complex ? 'p.h.p' : 'nothing', ",
			"      and => true,",
			"      is_also => \$multiline,",
			'xml:lang => "add \"hardness\"= {, \" ",',
			':src  => "javascripts/script_#{3 + 7}"}'
		)));
		
		$this->assertEquals("<p <?php atts(array('id' => array('this', \$item_here), 'has' => \$complex ? 'p.h.p' : 'nothing', 'and' => 'and', 'is_also' => \$multiline, 'xml:lang' => \"add \\\"hardness\\\"= {, \\\" \", 'src' => \"javascripts/script_\".(3 + 7))); ?>></p>", $html, 'Failed for multiline attributes with 3 attributes and the middle one containning PHP code');
	}

	public function testClassAndId()
	{
		$user = new stdClass();
		$user->id = 123;
		
		$tests = array(
			'.fancy[$user]' => '<div id="<?php echo id_for($user, \'\'); ?>" class="<?php echo \'fancy \'.class_for($user, \'\'); ?>"></div>',
			'.fancy[$user, old]' => '<div id="<?php echo id_for($user, \'old\'); ?>" class="<?php echo \'fancy \'.class_for($user, \'old\'); ?>"></div>',
			'#adiv' => '<div id="adiv"></div>',
			'#adiv(class = \'aasd\')' => '<div id="adiv" class="aasd"></div>',
			'#adiv.aclass' => '<div id="adiv" class="aclass"></div>',
			'#adiv.aclass.another' => '<div id="adiv" class="aclass another"></div>',
			'#adiv.aclass(id="two")' => '<div id="adiv_two" class="aclass"></div>',
			'#one(id="two"){id => "three"}' => '<div id="one_two_three"></div>',
			'.one(class="two"){class => "three"}' => '<div class="one two three"></div>'
		);
		
		while(list($haml, $html) = each($tests))
		{
			$actual = trim($this->compiler->parseString($haml));

			$actual = $this->evaluator->evaluate($actual, array('user' => $user));
			$expected = $this->evaluator->evaluate($html, array('user' => $user));
			
			$this->compareXmlStrings($expected, $actual);
		}
	}

	public function testInterpolationInsideHash()
	{
		$html = trim($this->compiler->parseString('%script{:type => "text/javascript", :src => "#{url::site(\'files/js/html5.js\')}"}'));
		$this->assertEquals('<script type="text/javascript" src="<?php echo url::site(\'files/js/html5.js\'); ?>"></script>', $html);
		
		$el = new Element('%script{:type => "text/javascript", :src => "#{url::site(\'files/js/html5.js\')}"}', $this->compiler);
		$this->assertEquals(array(
			'type' => array(
				't' => 'str',
				'v' => '"text/javascript"'
			),
			'src' => array(
				't' => 'str',
				'v' => '"#{url::site(\'files/js/html5.js\')}"'
			)
		), $el->getAttributes());
		
		$html = trim($this->compiler->parseString('%script{:type => "text/javascript", :src => {url::site(\'files/js/html5.js\')}}'));
		$this->assertEquals("<script <?php atts(array('type' => \"text/javascript\", 'src' => url::site('files/js/html5.js'))); ?>></script>", $html);
	}

	public function testInterpolationInsideBraces()
	{
		$el = new Element('%script(type = "text/javascript" src = "#{url::site(\'files/js/html5.js\')}")', $this->compiler);
		$this->assertEquals(array(
			'type' => array(
				't' => 'str',
				'v' => '"text/javascript"'
			),
			'src' => array(
				't' => 'str',
				'v' => '"#{url::site(\'files/js/html5.js\')}"'
			)
		), $el->getAttributes());
		$html = trim($this->compiler->parseString('%script(type = "text/javascript" src = "#{url::site(\'files/js/html5.js\')}")'));
		$this->assertEquals('<script type="text/javascript" src="<?php echo url::site(\'files/js/html5.js\'); ?>"></script>', $html);
	}
}

function p_atts()
{
	return array('lang' => 'en');
}

function user_atts($userId)
{
	return array('data-id' => $userId);
}

function teatcher_atts($userId, $viewerId)
{
	return array('data-grade' => '7th');
}