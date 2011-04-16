<?php

require_once 'NodeFactory.php';
require_once 'RootNode.php';

require_once 'Filter/FilterContainer.php';
require_once 'Filter/CssFilter.php';
require_once 'Filter/PlainFilter.php';
require_once 'Filter/JavascriptFilter.php';
require_once 'Filter/PhpFilter.php';

class Compiler
{
  private $_hamlphp = null;
  private $_lines;
  private $_currLine;

  public function __construct(HamlPHP $hamlphp)
  {
    $this->setHamlPhp($hamlphp);
  }

  public function setHamlPhp(HamlPHP $hamlphp)
  {
    $this->_hamlphp = $hamlphp;
  }

  /**
   * Compiles haml from a file.
   *
   * @param string $fileName
   */
  public function parseFile($fileName)
  {
    return $this->parseString(file_get_contents($fileName));
  }

  /**
   * Compiles haml from a string.
   *
   * @param string $rawString
   */
  public function parseString($rawString)
  {
    $lines = explode("\n", trim((string) $rawString));
    return $this->parseLines($lines);
  }

  /**
   * Compiles haml from an array of lines.
   *
   * @param array $rawLines
   */
  public function parseLines(array $rawLines = array())
  {
  	$this->_currLine = 0;
  	$this->_lines = $rawLines;
    $rootNode = new RootNode();
    $nodeFactory = $this->_hamlphp->getNodeFactory();

    for ($len = count($rawLines); $this->_currLine < $len; ++$this->_currLine) {
      $rootNode->addNode($nodeFactory->createNode($rawLines[$this->_currLine], $this));
    }

    return $rootNode->render();
  }

  public function getNextLine() {
    $index = ++$this->_currLine;

  	if (isset($this->_lines[$index])) {
      return $this->_lines[$index];
  	}

  	return null;
  }
}