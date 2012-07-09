<?php

require_once 'BaseTestCase.php';

/**
 * test case.
 */
class HamlPHPClassTest extends BaseTestCase
{
	public function testReadmeExample()
	{
		require_once HAMLPHP_ROOT . 'HamlPHP.php';
		require_once HAMLPHP_ROOT . 'Storage/FileStorage.php';
		
		// Make sure that a directory _tmp_ exists in your application and it is writable.
		$parser = new HamlPHP(new FileStorage(TEST_TMP_DIR));

		$actual = $parser->parseFile($this->getTemplatePath('readme_example'));
		$expected = $this->getExpectedResult('readme_example');
		
		$actual = $this->evaluator->evaluate($actual);
		$expected = $this->evaluator->evaluate($expected);
		
		$this->compareXmlStrings($expected, $actual);
	}

}

