<?php

require_once 'BaseTestCase.php';
require_once HAMLPHP_ROOT . '/ContentEvaluator/EvaluatorProxy.php';

class EvaluatorClassesHelper extends ContentEvaluator 
{
	public $pubProp = 'public';
	protected $prtProp = 'protected';
	private $prvProp = 'private';

	/**
	 * @var EvaluatorProxy
	 */
	private $evaluator;
	
	public function __construct()
	{
		$this->evaluator = new EvaluatorProxy($this);
	}
	
	public function proxyEvaluate($content, array $contentVariables = array())
	{
		return $this->evaluator->evaluate($content, $contentVariables);
	}

	public function proxyEvaluateFile($filepath, array $contentVariables = array())
	{
		return $this->evaluator->evaluate_file($filepath, $contentVariables);
	}
}