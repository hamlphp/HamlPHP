<?php

require_once 'ContentEvaluator.php';

class DefaultContentEvaluator implements ContentEvaluator
{
  public function evaluate($content, array &$contentVariables = array(), $id = null)
  {
    $tempFileName = tempnam("/tmp", "foo");
    $fp = fopen($tempFileName, "w");
    fwrite($fp, $content);

    ob_start();
    extract($contentVariables);
    require $tempFileName;
    foreach($contentVariables as $key => $value){
      if(empty($value) && !empty($$key)){
        $contentVariables[$key] = $$key;
      }
    }
    $result = ob_get_clean();

    fclose($fp);
    unlink($tempFileName);
    return $result;
  }
}