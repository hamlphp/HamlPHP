<?php

class RootNode
{
  private $_children;
  private $_indentation;
  private $_childrenCount;
  private $_parent;

  public function __construct()
  {
    $this->_children = array();
    $this->_indentation = -1;
    $this->_childrenCount = 0;
    $this->_parent = null;
  }

  public function setParent(RootNode $node)
  {
    $this->_parent = $node;
  }

  public function getParent()
  {
    return $this->_parent;
  }

  public function hasParent()
  {
    return $this->_parent !== null;
  }

  public function setIndentationLevel($level)
  {
    $this->_indentation = $level;
  }

  public function getIndentationLevel()
  {
    return $this->_indentation;
  }

  public function addNode(RootNode $node = null)
  {
    if ($node === null) {
      return false;
    }

    if ($this->shouldGoInsideLastNode($node)) {
      $lastNode = $this->_children[$this->_childrenCount - 1];
      $lastNode->addNode($node);
    } else {
      $node->setParent($this);
      $this->_children[] = $node;
      ++$this->_childrenCount;
      return true;
    }

    return false;
  }

  protected function shouldGoInsideLastNode($node)
  {
    if ($this->_childrenCount == 0) {
      return false;
    }

    $lastNode = $this->_children[$this->_childrenCount - 1];

    return $node->getIndentationLevel() > $lastNode->getIndentationLevel()
        || $lastNode->shouldContain($node);
  }

  public function getChildren()
  {
    return $this->_children;
  }

  public function hasChildren()
  {
    return $this->_childrenCount > 0;
  }

  public function renderChildren()
  {
    $output = '';

    for ($i = 0; $i < $this->_childrenCount; ++$i) {
      $output .= $this->_children[$i]->render();
    }

    return $output;
  }

  public function render()
  {
    return $this->renderChildren();
  }

  public function shouldContain($node)
  {
    return false;
  }
}