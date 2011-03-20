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
  private $_nodeFactory = null;

  public function __construct(NodeFactory $factory = null)
  {
    $this->_nodeFactory = $factory == null ? new NodeFactory() : $factory;
    $this->initializeNodeFactory();
  }

  public function getFilterContainer()
  {
    $filterContainer = new FilterContainer();
    $filterContainer->addFilter(new CssFilter());
    $filterContainer->addFilter(new PlainFilter());
    $filterContainer->addFilter(new JavascriptFilter());
    $filterContainer->addFilter(new PhpFilter());

    return $filterContainer;
  }

  protected function initializeNodeFactory()
  {
    $this->_nodeFactory->setFilterContainer($this->getFilterContainer());
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
    $rootNode = new RootNode();

    for ($i = 0, $len = count($rawLines); $i < $len; ++$i) {
      $rootNode->addNode($this->_nodeFactory->createNode($rawLines[$i]));
    }

    return $rootNode->render();
  }
}