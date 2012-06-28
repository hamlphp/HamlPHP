<?php

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'test/AttributesTest.php';
require_once 'test/CommentNodeTest.php';
require_once 'test/CompilerTest.php';
require_once 'test/ElementNodeTest.php';
require_once 'test/FilterTest.php';
require_once 'test/HamlNodeTest.php';
require_once 'test/HelpersTest.php';
require_once 'test/InterpolationTest.php';
require_once 'test/StringScannerTest.php';
require_once 'test/TagNodeTest.php';
require_once 'test/TryHamlTest.php';
require_once 'test/HamlPHPClassTest.php';

/**
 * Static test suite.
 */
class AllTests extends PHPUnit_Framework_TestSuite
{

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName('AllTests');
		$this->addTestSuite('AttributesTest');
		$this->addTestSuite('CommentNodeTest');
		$this->addTestSuite('CompilerTest');
		$this->addTestSuite('ElementNodeTest');
		$this->addTestSuite('FilterTest');
		$this->addTestSuite('HamlNodeTest');
		$this->addTestSuite('HelpersTest');
		$this->addTestSuite('InterpolationTest');
		$this->addTestSuite('StringScannerTest');
		$this->addTestSuite('TagNodeTest');
		$this->addTestSuite('TryHamlTest');
		$this->addTestSuite('TryHamlTest');
	}

	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

