<?php

require_once HAMLPHP_ROOT.'Lang/Helpers.php';

interface IContentEvaluator
{
  /**
   * Evaluates a string and returns the evaluation result.
   *
   * @param $content Contains HAML and PHP code.
   * @param $contentVariables an array of variables
   * @return string The evaluation output
   */
  public function evaluate($content, array $contentVariables = array());
  
  /**
   * Evaluates a file and returns the evaluation result.
   * 
   * @param string $filePath The path to file to be evaluated
   * @param array $contentVariables The variables to pass to the template
   * @return string The evaluation output
   */
  public function evaluateFile($filePath, array $contentVariables = array());
}