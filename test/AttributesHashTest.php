<?php

require_once 'BaseTestCase.php';

class AttributesHashTest extends BaseTestCase
{
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
		
		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);
		
		$this->compareXmlStrings($expected, $actual);
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
		$expected = '<div id="ItemType_4" class="ItemType immediate"></div>';

		$actual = $this->evaluator->evaluate($actual, array('item' => $item));
		$expected = $this->evaluator->evaluate($expected, array('item' => $item));
		
		$this->compareXmlStrings($expected, $actual);
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

		$actual = $this->evaluator->evaluate($actual);
		$expected = '<sandwich delicious="delicious" bread="whole wheat" filling="peanut butter and jelly" />';
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_interpolation()
	{
		$actual = trim($this->compiler->parseString('%p{title => "I have a #{$adjective} title"}'));
		$expected = '<p title="I have a <?php echo $adjective; ?>title"></p>';

		$actual = $this->evaluator->evaluate($actual, array('adjective' => 'nice'));
		$expected = $this->evaluator->evaluate($expected, array('adjective' => 'nice'));
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_expression()
	{
		$actual = trim($this->compiler->parseString('%p{title => {"I have a $adjective title"}}'));
		$expected = '<p <?php atts(array(\'title\' => "I have a $adjective title")); ?>></p>';

		$actual = $this->evaluator->evaluate($actual, array('adjective' => 'nice'));
		$expected = $this->evaluator->evaluate($expected, array('adjective' => 'nice'));
		
		$this->compareXmlStrings($expected, $actual);
		
		$actual = trim($this->compiler->parseString('%p{title => {"I have a " . $adjective . " title"}}'));
		$expected = '<p <?php atts(array(\'title\' => "I have a " . $adjective . " title")); ?>></p>';

		$actual = $this->evaluator->evaluate($actual, array('adjective' => 'nice'));
		$expected = $this->evaluator->evaluate($expected, array('adjective' => 'nice'));
		
		$this->compareXmlStrings($expected, $actual);
		
		$actual = trim($this->compiler->parseString("%p{title => {youCanDo('anything') + 'here'}}"));
		$expected = '<p <?php atts(array(\'title\' => youCanDo(\'anything\') + \'here\')); ?>></p>';

		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_literals()
	{
		$actual = trim($this->compiler->parseString('%button{disabled => true}'));
		$expected = '<button disabled="disabled"></button>';

		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function Attribute_value_allows_variables()
	{
		$user = new stdClass();
		$user->name = 'Chuck Norris';
		
		$actual = trim($this->compiler->parseString('%button{disabled => $disabled}'));
		$expected = '<button disabled="<?php echo $disabled; ?>"></button>';

		$actual = $this->evaluator->evaluate($actual, array('disabled' => true));
		$expected = $this->evaluator->evaluate($expected, array('disabled' => true));
		
		$this->compareXmlStrings($expected, $actual);
		
		$actual = trim($this->compiler->parseString('%input{value => $user->name, name => "name"}'));
		$expected = '<input value="<?php echo $user->name; ?>" name="name"></input>';

		$actual = $this->evaluator->evaluate($actual, array('user' => $user));
		$expected = $this->evaluator->evaluate($expected, array('user' => $user));
		
		$this->compareXmlStrings($expected, $actual);
		
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

		$actual = $this->evaluator->evaluate($actual, array('user' => $user));
		$expected = $this->evaluator->evaluate($expected, array('user' => $user));
		
		$this->compareXmlStrings($expected, $actual);
		
		// evaluation
		$evaluated = trim($this->evaluator->evaluate($actual, array('user' => $user)));
		$output = '<div id="stdClass_1234" class="panel"></div>';

		$actual = $this->evaluator->evaluate($actual, array('user' => $user));
		$expected = $this->evaluator->evaluate($expected, array('user' => $user));
		
		$this->compareXmlStrings($expected, $actual);
	}
	
	/**
	 * @test
	 */
	public function Multiple_class_definitions_are_concatenated()
	{
		$user = new stdClass();
		$user->id = 1234;
		
		$actual = trim($this->compiler->parseString('.panel{ :class => class_for($user) }'));
				
		$actual = $this->evaluator->evaluate($actual, array('user' => $user));
		$expected = '<div class="panel std_class"></div>';
		
		$this->compareXmlStrings($expected, $actual);
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

		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);
		
		$this->compareXmlStrings($expected, $actual);
	}
}

function youCanDo($action)
{
	return "You can do $action";
}

function hash1() {
	return array('bread' => 'white', 'filling' => 'peanut butter and jelly');
}

function hash2() {
	return array('bread' => 'whole wheat');
}