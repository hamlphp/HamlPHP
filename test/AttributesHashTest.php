<?php

require_once 'test_helper.php';
require_once HAMLPHP_ROOT . 'ContentEvaluator/DefaultContentEvaluator.php';

class AttributesHashTest extends PHPUnit_Framework_TestCase
{
	protected $compiler = null;
	protected $evaluator = null;

	public function setUp() {
		$this->compiler = getTestCompiler();
		$this->evaluator = new DefaultContentEvaluator();
	}

	/**
	 * @test Hashes can stretched over multiple lines
	 * 
	 * Attribute hashes can be stretched out over multiple lines to accommodate many attributes.
	 * However, newlines may only be placed immediately after commas.
	 */
	public function Hashes_can_stretched_over_multiple_lines()
	{
		// multiline
		$actual = trim($this->compiler->parseLines(array(
			'%script{type => "text/javascript"',
			'		src => "javascripts/script_#{2 + 7}"}/'
		)));
		
		$expected = '<script type="text/javascript" src="javascripts/script_<?php echo 2 + 7; ?>" />';
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @test Class and id attributes can be arrays
	 * 
	 * The class and id attributes can also be specified as an array whose elements will be joined together. 
	 * A class array is joined with " " and an id array is joined with "_". For example:
	 *     %div{id => [$item->type, $item->number], class => [$item->type, $item->urgency]}
	 * is equivalent to:
	 *     %div{id => "#{$item->type}_#{$item->number}", class => "#{$item->type} #{$item->urgency}"}
	 * both render to:
	 *     <div <?php atts(array('id' => array($item->type, $item->number), 'class' => array($item->type, $item->urgency))); ?>></div>
	 * and would be evaluated to:
	 *     <div id="ItemType_4 class="ItemType immediate></div>
	 */
	public function Class_and_id_attributes_can_be_arrays()
	{
		$item = new stdClass();
		$item->type = 'ItemType';
		$item->number = 4;
		$item->urgency = 'immediate';
		
		$actual = trim($this->compiler->parseString('%div{:id => [$item->type, $item->number], :class => [$item->type, $item->urgency]}'));
		$expected = "<div <?php atts(array('id' => array(\$item->type, \$item->number), 'class' => array(\$item->type, \$item->urgency))); ?>></div>";
		$this->assertEquals($expected, $actual);

		$output = $this->evaluator->evaluate($expected, array('item' => $item));
		$this->assertEquals('<div id="ItemType_4 class="ItemType immediate></div>', $output);
	}
	
	/**
	 * @test Attribute list can be replaced by a function
	 * 
	 * A function or method call that returns an array map (key => value) can be substituted for the hash contents. 
	 * You can use as many such attribute methods as you want by separating them with commas. 
	 * All the hashes will me merged together, from left to right.
	 * 
	 * <pre>
	 * 	function hash1() {
	 * 		return array('bread' => 'white', 'filling' => 'peanut butter and jelly');
	 * 	}
	 * 
	 * 	function hash2() {
	 * 		return array('bread' => 'whole wheat');
	 * 	}
	 * </pre>
	 * 
	 * then
	 *     %sandwich{hash1(), hash2(), delicious => true}/
	 * would compile to:
	 *     &lt;sandwich <?php atts(array(hash1(), hash2(), 'delicious' => 'delicious')); ?> />
	 * And evaluate to:
	 *     &lt;sandwich bread='whole wheat' filling='peanut butter and jelly' delicious='true' />
	 */
	public function Attribute_list_can_be_replaced_by_a_function()
	{		
		$actual = $this->compiler->parseString('%sandwich{hash1(), hash2(), delicious => true}/');
		
		$expected = "<sandwich <?php atts(array(hash1(), hash2(), 'delicious' => 'delicious')); ?> />";
		
		$evaluated = $this->evaluator->evaluate($expected);
		$this->assertEquals(
			'<sandwich delicious="delicious" bread="whole wheat" filling="peanut butter and jelly" />', 
			$evaluated);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_interpolation()
	{
		$actual = trim($this->compiler->parseString('%p{title => "I have a #{$adjective} title"}'));
		$this->assertEquals('<p title="I have a <?php echo $adjective; ?>title"></p>', $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_expression()
	{
		$actual = trim($this->compiler->parseString('%p{title => {"I have a $adjective title"}}'));
		$this->assertEquals('<p <?php atts(array(\'title\' => "I have a $adjective title")); ?>></p>', $actual);
		
		$actual = trim($this->compiler->parseString('%p{title => {"I have a " . $adjective . " title"}}'));
		$this->assertEquals('<p <?php atts(array(\'title\' => "I have a " . $adjective . " title")); ?>></p>', $actual);
		
		$actual = trim($this->compiler->parseString("%p{title => {youCanDo('anything') + 'here'}}"));
		$this->assertEquals('<p <?php atts(array(\'title\' => youCanDo(\'anything\') + \'here\')); ?>></p>', $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_literals()
	{
		$actual = trim($this->compiler->parseString('%button{disabled => true}'));
		$this->assertEquals('<button disabled="disabled"></button>', $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_variables()
	{
		$actual = trim($this->compiler->parseString('%button{disabled => $disabled}'));
		$this->assertEquals('<button disabled="<?php echo $disabled; ?>"></button>', $actual);
		$actual = trim($this->compiler->parseString('%input{value => $user->name, name => "name"}'));
		$this->assertEquals('<input value="<?php echo $user->name; ?>" name="name"></input>', $actual);
		
	}
	
	/**
	 * @test
	 */
	public function Multiple_id_definitions_are_concatenated()
	{
		$user = new stdClass();
		$user->id = 1234;
		
		// compilation
		$actual = trim($this->compiler->parseString('#stdClass.panel{ :id => $user->id }'));
		$expected = "<div <?php atts(array('id' => 'stdClass_'.\$user->id , 'class' => 'panel')); ?>></div>";

		$this->assertEquals($expected, $actual, "Compilation failed.");
		
		// evaluation
		$evaluated = trim($this->evaluator->evaluate($actual, array('user' => $user)));
		$output = '<div id="stdClass_1234" class="panel"></div>';
		
		$this->assertEquals($output, $evaluated, "Evaluation Failed.");
	}
	
	/**
	 * @test
	 */
	public function Multiple_class_definitions_are_concatenated()
	{
		$user = new stdClass();
		$user->id = 1234;
		
		// compilation
		$actual = trim($this->compiler->parseString('.panel{ :class => class_for($user) }'));
		$expected = "<div <?php atts(array('class' => 'panel '.class_for(\$user))); ?>></div>";

		$this->assertEquals($expected, $actual, "Compilation failed.");
		
		// evaluation
		$evaluated = trim($this->evaluator->evaluate($actual, array('user' => $user)));
		$output = '<div class="panel stdClass"></div>';
		
		$this->assertEquals($output, $evaluated, "Evaluation Failed.");
	}
	
	/**
	 * @test
	 * 
	 * Custom data attributes can be used in Haml by using the key data with a Hash value in an attribute hash. 
	 * Each of the key/value pairs in the Hash will be transformed into a custom data attribute. For example:
	 * 
	 * %a{href=>"/posts", data => {author_id => 123}} Posts By Author
	 * 
	 * will render as:
	 * 
	 * <a data-author_id='123' href='/posts'>Posts By Author</a>
	 */
	public function Custom_data_attribute_value_accepts_a_hash()
	{
		$actual = trim($this->compiler->parseString('%a{:href=>"/posts", :data => {author_id => "123"} } Posts By Author'));
		$expected = '<a href="/posts" data-author_id="123">Posts By Author</a>';
		
		$this->assertEquals($expected, $actual, "Compilation failed.");
	}
}

function hash1() {
	return array('bread' => 'white', 'filling' => 'peanut butter and jelly');
}

function hash2() {
	return array('bread' => 'whole wheat');
}