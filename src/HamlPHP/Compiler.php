<?php

require_once 'NodeFactory.php';
require_once 'RootNode.php';

class Compiler
{
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
      $rootNode->addNode(NodeFactory::createNode($rawLines[$i]));
    }

    return $rootNode->render();
  }
}