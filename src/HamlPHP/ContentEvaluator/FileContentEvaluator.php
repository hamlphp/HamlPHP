<?php

require_once 'ContentEvaluator.php';

/**
 *
 * @author svallory
 *        
 */
class FileContentEvaluator implements ContentEvaluator
{
	/**
	 * @param $content 
	 * @param $contentVariables an array of variables
	 * @param $id An identifier of the content. Could be a filename.
	 *        	
	 * @return string
	 *
	 * @see ContentEvaluator::evaluate()
	 *
	 */
	public function evaluate($content, array $contentVariables = array(), $id = null)
	{
		if(null === $id)
		{
			throw new Exception("FileStorage: Could not evaluate. ID is null.");
		}
		
		$this->_requirePath = $this->_path . $id . $this->_extension;
		
		ob_start();
		extract($contentVariables);
		require $this->_requirePath;
		$result = ob_get_clean();
		
		return $result;
	}
}

?>