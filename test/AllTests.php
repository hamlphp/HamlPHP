<?php

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'AttributesHashTest.php';
require_once 'AttributesTest.php';
require_once 'CommentNodeTest.php';
require_once 'CompilerTest.php';
require_once 'ElementNodeTest.php';
require_once 'EvaluateFunctionsTest.php';
require_once 'FilterTest.php';
require_once 'HamlNodeTest.php';
require_once 'HamlPHPClassTest.php';
require_once 'HelpersTest.php';
require_once 'HtmlStyleAttributesTest.php';
require_once 'InterpolationTest.php';
require_once 'MarkdownFilterTest.php';
require_once 'ObjectReferenceTest.php';
require_once 'StringScannerTest.php';
require_once 'TagNodeTest.php';
require_once 'TryHamlTest.php';

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
		$this->addTestSuite('AttributesHashTest');
		$this->addTestSuite('AttributesTest');
		$this->addTestSuite('CommentNodeTest');
		$this->addTestSuite('CompilerTest');
		$this->addTestSuite('ElementNodeTest');
		$this->addTestSuite('EvaluateFunctionsTest');
		$this->addTestSuite('FilterTest');
		$this->addTestSuite('HamlNodeTest');
		$this->addTestSuite('HamlPHPClassTest');
		$this->addTestSuite('HelpersTest');
		$this->addTestSuite('HtmlStyleAttributesTest');
		$this->addTestSuite('InterpolationTest');
		$this->addTestSuite('MarkdownFilterTest');
		$this->addTestSuite('ObjectReferenceTest');
		$this->addTestSuite('StringScannerTest');
		$this->addTestSuite('TagNodeTest');
		$this->addTestSuite('EvaluateFunctionsTest');
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

