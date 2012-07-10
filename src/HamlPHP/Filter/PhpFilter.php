<?php

require_once 'IHamlFilter.php';

class PhpFilter implements IHamlFilter
{
  public function getIdentifier()
  {
    return 'php';
  }

  public function filter(HamlNode $node)
  {
    if ($node === null) {
      throw new Exception('PHP filter: node is null');
    }

    $plainFilter = new PlainFilter();

    $output = $node->getSpaces() . "<?php\n";
    $output .= $plainFilter->filter($node);
    $output .= $node->getSpaces() . "?>";

    return $output . "\n";
  }
}
