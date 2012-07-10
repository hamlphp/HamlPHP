<?php

require_once 'IHamlFilter.php';

class CssFilter implements IHamlFilter
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

    $plainFilter = new PlainFilter();

    $output = $node->getSpaces() . "<style type=\"text/css\">\n";

    $oldLevel = $node->getIndentationLevel();
    $node->setIndentationLevel($oldLevel + 2);
    $output .= $plainFilter->filter($node);
    $node->setIndentationLevel($oldLevel);

    $output .= $node->getSpaces() . "</style>";

    return $output . "\n";
  }
}