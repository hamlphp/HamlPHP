<?php

require_once 'AbstractFilter.php';

class CssFilter extends AbstractFilter
{
  public function getIdentifier()
  {
    return 'css';
  }

  public function filter($text)
  {
    $filterNode = $this->getFilterNode();

    if (null === $filterNode) {
      throw new Exception("CssFilter: FilterNode is null.");
    }

    $output = $filterNode->getSpaces() . "<style type=\"text/css\">\n";
    $output .= $text;
    $output .= $filterNode->getSpaces() . "</style>";

    return $output . "\n";
  }
}