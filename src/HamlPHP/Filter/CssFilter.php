<?php

require_once 'Filter.php';

class CssFilter implements Filter
{
  public function getIdentifier()
  {
    return 'css';
  }

  public function filter(HamlNode $node)
  {
    if (null === $node) {
      throw new Exception("CssFilter: node is null.");
    }

    $output = $node->getSpaces() . "<style type=\"text/css\">\n";
    $output .= $node->renderChildren();
    $output .= $node->getSpaces() . "</style>";

    return $output . "\n";
  }
}