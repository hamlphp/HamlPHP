<?php

abstract class AbstractFilter implements Filter
{
  private $_filterNode = null;

  public function setFilterNode($node)
  {
    $this->_filterNode = $node;
  }

  public function getFilterNode()
  {
    return $this->_filterNode;
  }
}