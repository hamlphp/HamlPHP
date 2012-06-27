<?php

require_once HAMLPHP_ROOT.'Lang/Helpers.php';

interface ContentEvaluator
{
  /**
   * Evaluates a string.
   *
   * @param $content Contains HAML and PHP code.
   * @param $contentVariables an array of variables
   * @param $id An identifier of the content. Could be a filename.
   * @return string
   */
  public function evaluate($content, array $contentVariables = array(), $id = null);
}