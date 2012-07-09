<?php

require_once 'BaseTestCase.php';
require_once HAMLPHP_ROOT . '/Lang/Interpolation.php';

class InterpolationTest extends BaseTestCase
{
	public function testInterpolationTemplate()
	{
		$actual = $this->compiler->parseFile( $this->getTemplatePath('interpolation'));
		$expected = $this->getExpectedResult('interpolation');

	    $actual = $this->evaluator->evaluate($actual);
	    $expected = $this->evaluator->evaluate($expected);
	    
	    $this->compareXmlStrings($expected, $actual);
	}
	
	public function Should_work_inside_comments()
	{
		$haml = <<<END
%p
  / #{ 'this should be inside PHP' }
END;
		$expected = <<<END
<p>
  <!-- <?php echo 'this should be inside PHP'; ?> -->
</p>
END;
		
		$actual = $this->compiler->parseString($haml);
		
		$this->assertEquals($expected, $actual);
	}

	public function testInterpolation()
	{
		$interpolation = new Interpolation("#{1 + 1}");
		$this->assertEquals("<?php echo 1 + 1; ?>", $interpolation->render());
		
		$interpolation = new Interpolation("test #{'hello'} #{'world'}.");
		$this->assertEquals("test <?php echo 'hello'; ?> <?php echo 'world'; ?>.", $interpolation->render());

		$interpolation = new Interpolation("p { color: #{'black'}; }");
		$this->assertEquals("p { color: <?php echo 'black'; ?>; }", $interpolation->render());
		
	}

	/**
	 * @expectedException Exception
	 */
	public function testEmptyInterpolationThrows()
	{
		$interpolation = new Interpolation("#{}");
		$interpolation->render();
	}

	public function testUnclosedInterpolationThrows()
	{
		try
		{
			$interpolation = new Interpolation("#{1 + 1");
			$interpolation->render();
			
			// should not get here
			$this->fail("testUnclosedInterpolationThrows: did not throw");
		}
		catch(SyntaxErrorException $e)
		{
			// do nothing
		}
		
		try
		{
			$interpolation = new Interpolation("test #{'jei");
			$interpolation->render();
			
			// should not get here
			$this->fail("testUnclosedInterpolationThrows: did not throw");
		}
		catch(SyntaxErrorException $e)
		{
			// do nothing
		}
	}

	/**
	 * @expectedException SyntaxErrorException
	 */
	public function testNestedInterpolationThrows()
	{
		$interpolation = new Interpolation("test #{1 + 1 #{2 + 2}}");
		$interpolation->render();
	}

	public function testRenderInsidePhp()
	{
		$i = new Interpolation("#{ImTheWholeThing()}", true);
		$actual = $i->render();
		$this->assertEquals('ImTheWholeThing()', $actual);
		
		$i = new Interpolation("\"Hey, look! #{ImInsidePhp()}! And #{\$me + \$too}!\"", true);
		$actual = $i->render();
		$this->assertEquals('"Hey, look! ".(ImInsidePhp())."! And ".($me + $too)."!"', $actual);
		
	}
}
