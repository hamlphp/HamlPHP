<?php

require_once 'BaseTestCase.php';
require_once 'mocks/EvaluatorClassesHelper.php';

/**
 * evaluate() test case.
 */
class EvaluateFunctionsTest extends BaseTestCase
{
	private $template;

	private $result;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$this->template = <<<END
<?php
		echo \$this->pubProp." ";
		
		if(!empty(\$this->prtProp))
			echo \$this->prtProp." ";
			
		if(!empty(\$this->pvtProp))
			echo \$this->pvtProp." ";
END;
	}

	/**
	 * @test
	 * Tests EvaluatorProxy::evaluate
	 */
	public function proxy_evaluator_should_access_only_public_properties()
	{
		$ctrl = new EvaluatorClassesHelper();
		
		$actual = trim($ctrl->proxyEvaluate($this->template));
		
		$this->assertEquals('public', $actual);
	}
	
	/**
	 * @test
	 * Tests ContentEvaluator::evaluate
	 */
	public function inherited_evaluate_should_access_public_and_protected_properties()
	{
		$ctrl = new EvaluatorClassesHelper();
		
		$actual = trim($ctrl->evaluate($this->template));
		
		$this->assertEquals('public protected', $actual);
	}
}