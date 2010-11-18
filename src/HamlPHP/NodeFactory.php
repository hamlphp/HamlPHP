<?php

require_once 'ElementNode.php';
require_once 'HamlNode.php';
require_once 'DoctypeNode.php';
require_once 'TagNode.php';

class NodeFactory
{
  const ELEMENT = '%';
  const ID = '#';
  const KLASS = '.';

  const HTML_COMMENT = '/';
  const HAML_COMMENT = '-#';
  const DOCTYPE = '!!!';

  const VARIABLE = '=';
  const TAG = '-';

  static public function createNode($line)
  {
    $strippedLine = trim($line);

    if ($strippedLine === '') {
      return null;
    }

    if (strpos($strippedLine, NodeFactory::DOCTYPE, 0) !== false) {
      return new DoctypeNode($line);
    }

    $elements = array(
      NodeFactory::ELEMENT,
      NodeFactory::ID,
      NodeFactory::KLASS,
    );

    if (in_array($strippedLine[0], $elements)) {
      return new ElementNode($line);
    }

    if ($strippedLine[0] === NodeFactory::TAG) {
      return new TagNode($line);
    }

    return new HamlNode($line);
  }
}
