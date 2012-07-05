<?php

require_once 'test_helper.php';
require_once HAMLPHP_ROOT . 'ContentEvaluator/DefaultContentEvaluator.php';

/**
 * test case
 * Square brackets follow a tag definition and contain a PHP object that is used 
 * to set the class and id of that tag. The class is set to the objectÕs class 
 * (transformed to use underlines rather than camel case) and the id is set to 
 * the objectÕs class, followed by its id. Because the id of an object is 
 * normally an obscure implementation detail, this is most useful for elements 
 * that represent instances of Models. 
 * 
 * Additionally, the second argument (if present) will be used as a prefix for 
 * both the id and class attributes.
 */
class ObjectReferenceTest extends PHPUnit_Framework_TestCase
{
	protected $compiler = null;
	protected $evaluator = null;

	public function setUp() {
		$this->compiler = getTestCompiler();
		$this->evaluator = new DefaultContentEvaluator();
	}
	

	/**
	 * @test
	 * 
	 * The class is set to the objectÕs class (transformed to use underlines rather than camel case) 
	 * and the id is set to the objectÕs class, followed by its id.
	 */
	public function transforms_class_and_id()
	{
		$user = new stdClass();
		
		$actual = trim($this->compiler->parseString('.fancy[$user]'));
		$evaluated = $this->evaluator->evaluate($actual, array('user' => $user));
		echo $actual."\n";
		$output = '<div id="std_class_1" class="fancy std_class"></div>';
		
		$this->assertEquals($output, $evaluated);
	}
	
	/**
	 * @test
	 * 
	 * Additionally, the second argument (if present) 
	 * will be used as a prefix for both the id and class attributes. 
	 */
	public function can_define_prefixes()
	{
		$user = new stdClass();
		$user->id = 1;
		
		$actual = trim($this->compiler->parseString('.fancy[$user, :this_is]'));
		$evaluated = $this->evaluator->evaluate($actual, array('user' => $user));
		$output = '<div id="this_is_std_class_1" class="fancy this_is_std_class"></div>';
		echo $actual."\n";
		$this->assertEquals($output, $evaluated);
	}
	
	/**
	 * @test
	 * 
	 * If you require that the class be something other than the underscored objectÕs class, 
	 * you can implement the haml_object_ref method on the object.
	 */
	public function allow_class_name_to_be_overriden()
	{		
		$user = new ClassWithMethod_hamlObjRef();
		
		$actual = trim($this->compiler->parseString('.fancy[$user]'));
		$evaluated = $this->evaluator->evaluate($actual, array('user' => $user));
		$output = '<div id="user_1" class="fancy user"></div>';
		$this->assertEquals($output, $evaluated);
		echo $actual."\n";
		$user = new ClassWithMethod_haml_obj_ref();
		
		$actual = trim($this->compiler->parseString('.fancy[$user]'));
		$evaluated = $this->evaluator->evaluate($actual, array('user' => $user));
		$output = '<div id="user_2" class="fancy user"></div>';
		echo $actual."\n";
		$this->assertEquals($output, $evaluated);
	}
	
	/**
	 * @test
	 */
	public function should_call_getId_when_no_property_id_is_found()
	{
		$user = new ClassWithMethod_getId();
		
		$actual = trim($this->compiler->parseString('.fancy[$user]'));
		$evaluated = $this->evaluator->evaluate($actual, array('user' => $user));
		$output = '<div id="user_123" class="fancy user"></div>';
		echo $actual."\n";
		$this->assertEquals($output, $evaluated);
	}
}

class ClassWithMethod_getId
{
	public function hamlObjRef()
	{
		return 'user';
	}
	
	public function getId()
	{
		return 123;
	}
}
class ClassWithMethod_hamlObjRef
{
	public function hamlObjRef()
	{
		return 'user';
	}
}

class ClassWithMethod_haml_obj_ref
{
	public function haml_obj_ref()
	{
		return 'user';
	}
}
