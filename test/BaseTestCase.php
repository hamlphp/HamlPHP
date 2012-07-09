<?php

require_once 'src/HamlPHP/Config.php';
require_once HAMLPHP_ROOT . 'HamlPHP.php';
require_once HAMLPHP_ROOT . 'Storage/FileStorage.php';
require_once HAMLPHP_ROOT . 'Compiler.php';
require_once HAMLPHP_ROOT . 'ContentEvaluator/ContentEvaluator.php';

require_once 'XmlDiff/src/XmlDiff.php';

if(!defined('TEST_TMP_DIR'))
	define('TEST_TMP_DIR', dirname(__FILE__) . '/tmp/');

class BaseTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var ContentEvaluator
	 */
	protected $evaluator;
	
	/**
	 * @var Compiler
	 */
	protected $compiler;
	
	public function __construct()
	{
		$this->evaluator = new ContentEvaluator();
		
		$hamlPHP = new HamlPHP(new FileStorage(dirname(__FILE__) . '/tmp/'));
		$hamlPHP->disableCache();
		$this->compiler = $hamlPHP->getCompiler();
	}
	
	public function getTemplatePath($template) {
		return dirname(__FILE__) . '/templates/' . $template . '.haml';
	}
	
	public function getExpectedResult($template) {
		return file_get_contents(dirname(__FILE__) . '/templates/' . $template . '_expected.php');
	}
	
	public function compareXmlStrings($expected, $actual)
	{
		$docExpected = new DOMDocument();
		
		try {
			if(!$docExpected->loadXML($expected))
				$this->fail("Couldn't load expected xml into DOMDocument. The xml was: $actual");
		}
		catch (Exception $ex)
		{
			$this->fail("Couldn't load expected xml into DOMDocument. The xml was: $actual");
		}
		
		$docActual = new DOMDocument();

		try {
			if(!$docActual->loadXML($actual))
				$this->fail("Couldn't load actual xml into DOMDocument. The xml was: $actual");
		}
		catch (Exception $ex)
		{
			$this->fail("Couldn't load actual xml into DOMDocument. The xml was: $actual");
		}
		
		$differ = new XmlDiff($docExpected, $docActual);
		
		$delta = (string)$differ->diff();
		
		$this->assertEmpty($delta, "Differences found: $delta");
	} 
}

?>