<?php

require_once 'IHamlFilter.php';

class JavascriptFilter implements IHamlFilter
{
  public function getIdentifier()
  {
    return 'javascript';
  }

  public function filter(HamlNode $node)
  {
    if ($node === null) {
      throw new Exception('Javascript filter: node is null');
    }

    $plainFilter = new PlainFilter();

    $output = $node->getSpaces() . "<script type=\"text/javascript\">\n";

    $oldLevel = $node->getIndentationLevel();
    $node->setIndentationLevel($oldLevel + 2);
    $output .= $plainFilter->filter($node);
    $node->setIndentationLevel($oldLevel);

    $output .= $node->getSpaces() . "</script>";

    return $output . "\n";
  }
}