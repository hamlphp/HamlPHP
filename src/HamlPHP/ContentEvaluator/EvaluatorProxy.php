<?php

require_once 'ContentEvaluator.php';

/**
 * This file provides an evaluate function to help integrating HamlPHP in other environments.
 * If you use the evaluate function inside a class, all it's properties will be available 
 * to the template through the $this variable.
 */
class EvaluatorProxy extends ContentEvaluator
{
	/**
	 * @var object
	 */
	private $context;
	
	public function __construct($context)
	{
		$this->context = $context;
	}
	
	public function __get($name)
	{
		return $this->context->$name;
	}

	public function __set($name, $value)
	{
		$this->context->$name = $value;
	}
}