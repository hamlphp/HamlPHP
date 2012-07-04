<?php

require_once 'test_helper.php';

/**
 * test case.
 */
class HamlPHPClassTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->compiler = getTestCompiler();	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		// TODO Auto-generated TestNormalUse::tearDown()
		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	public function testReadmeExample()
	{
		require_once HAMLPHP_ROOT . 'HamlPHP.php';
		require_once HAMLPHP_ROOT . 'Storage/FileStorage.php';
		
		// Make sure that a directory _tmp_ exists in your application and it is writable.
		$parser = new HamlPHP(new FileStorage(TEST_TMP_DIR));
		
		$expected = contents(template('readme_example.php'));
		$actual = $parser->parseFile(template_path('readme_example'));
		
		$this->assertEquals($expected, $actual);
	}

}

