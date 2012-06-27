<?php

class HamlNode extends RootNode
{
  private $_rawHaml;
  private $_haml;
  private $_spaces;

  public function __construct($line)
  {
    parent::__construct();
    $this->_rawHaml = $line;
    $this->_haml = trim($line);
    $this->setIndentationLevel(strlen($line) - strlen(ltrim($line)));
  }

  public function getSpaces()
  {
    return $this->_spaces;
  }

  public function setIndentationLevel($level)
  {
    $oldLevel = $this->getIndentationLevel();
    parent::setIndentationLevel($level);
    $this->_spaces = $this->createSpaces();

    if ($this->hasChildren()) {
      $children = $this->getChildren();

      for ($i = 0, $len = count($children); $i < $len; $i++) {
        $childNode = $children[$i];
        $currentLevel = $this->getIndentationLevel();
        $childLevel = $childNode->getIndentationLevel();
        $oldDiff = $childLevel - $oldLevel;
        $newLevel = $currentLevel + $oldDiff;
        $childNode->setIndentationLevel($newLevel);
      }
    }
  }

  public function getRawHaml()
  {
    return $this->_rawHaml;
  }

  public function getHaml()
  {
    return $this->_haml;
  }

  private function createSpaces()
  {
    $spaces = '';

    for ($i = 0; $i < $this->getIndentationLevel(); ++$i) {
      $spaces .= ' ';
    }

    return $spaces;
  }

  public function render()
  {
    $output = $this->_spaces . $this->_haml . "\n";
    $interpolation = new Interpolation($output);
    $output = $interpolation->render();

    if ($this->hasChildren()) {
      $output = $output . $this->renderChildren();
    }

    return $output;
  }
}