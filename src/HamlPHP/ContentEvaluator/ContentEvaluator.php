<?php

require_once 'IContentEvaluator.php';

class ContentEvaluator implements IContentEvaluator
{
	public function evaluate($content, array $contentVariables = array())
	{
		$tempFileName = tempnam(HAMLPHP_ROOT."/tmp", "hamlphp");
		$fp = fopen($tempFileName, "w");
		fwrite($fp, $content);
		
		$result = $this->evaluateFile($tempFileName, $contentVariables);
		
		fclose($fp);
		unlink($tempFileName);
		return $result;
	}

	/**
	 * @param $content
	 * @param $contentVariables an array of variables
	 *
	 * @return string
	 *
	 * @see ContentEvaluator::evaluateFile()
	 *
	 */
	public function evaluateFile($filePath, array $contentVariables = array())
	{
		ob_start();
		extract($contentVariables);
		require $filePath;
		$result = ob_get_clean();
	
		return $result;
	}
}