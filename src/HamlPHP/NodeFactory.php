<?php
/**
 * This file should include all nodes in the Node package
 */
require_once 'Lang/Nodes/RootNode.php';
require_once 'Lang/Nodes/HamlNode.php';

require_once 'Lang/Nodes/CommentNode.php';
require_once 'Lang/Nodes/DoctypeNode.php';
require_once 'Lang/Nodes/ElementNode.php';
require_once 'Lang/Nodes/FilterNode.php';
require_once 'Lang/Nodes/TagNode.php';

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
  const FILTER = ':';
  const LOUD_SCRIPT = '=';

  private $_filterContainer = null;

  public function setFilterContainer(FilterContainer $container)
  {
    $this->_filterContainer = $container;
  }

  protected function getNodeObject($line, Compiler $compiler) {
    $strippedLine = trim($line);

    if($strippedLine == '')
    	return null;
    	
    if (strpos($strippedLine, self::FILTER, 0) === 0) {
      return new FilterNode($line, $this->_filterContainer);
    }

    if (strpos($strippedLine, NodeFactory::DOCTYPE, 0) !== false) {
      return new DoctypeNode($line);
    }

    if (substr($strippedLine, 0, 1) === NodeFactory::HTML_COMMENT
        || substr($strippedLine, 0, 2) === NodeFactory::HAML_COMMENT) {
      return new CommentNode($line);
    }

    $elements = array(
      NodeFactory::ELEMENT,
      NodeFactory::ID,
      NodeFactory::KLASS,
    );

    if (in_array($strippedLine[0], $elements)) {
      return new ElementNode($line, $compiler);
    }

    if ($strippedLine[0] === NodeFactory::TAG
        ||$strippedLine[0] === NodeFactory::LOUD_SCRIPT) {
      return new TagNode($line);
    }

    return new HamlNode($line);
  }

  public function createNode($line, $lineNumber, Compiler $compiler)
  {
    $node = $this->getNodeObject($line, $compiler);

    if ($node !== null) {
      $node->setLineNumber($lineNumber);
      $node->setCompiler($compiler);
    }

    return $node;
  }
}
