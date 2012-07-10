<?php

require_once 'IHamlFilter.php';

class PlainFilter implements IHamlFilter
{
  public function getIdentifier()
  {
    return 'plain';
  }

  public function filter(HamlNode $node)
  {
    if (null === $node) {
      throw new Exception("PlainFilter: node is null.");
    }

    $children = $node->getChildren();
    $output = '';

    foreach ($children as $childNode) {
      $output .= $this->renderChildrenHaml($childNode);
    } 

    return $output;
  }

  protected function renderChildrenHaml(HamlNode $node)
  {
    $parent = $node->getParent();
    $haml = $parent !== null
        ? $parent->getSpaces() . $node->getHaml() : $node->getRawHaml();

    $output = $haml . "\n";

    if ($node->hasChildren()) {
      $children = $node->getChildren();

      for ($i = 0, $count = count($children); $i < $count; ++$i) {
        $output .= $this->renderChildrenHaml($children[$i]);
      }
    }

    return $output;
  }
}