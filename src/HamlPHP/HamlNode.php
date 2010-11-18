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
    $this->setIndetationLevel(strlen($line) - strlen(ltrim($line)));
    $this->_spaces = $this->createSpaces();
  }

  public function getSpaces()
  {
    return $this->_spaces;
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
    return $this->_spaces . $this->_haml;
  }
}