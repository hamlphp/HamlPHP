<?php

class RootNode
{
  protected $_children;
  protected $_indentation;
  protected $_childrenCount;
  protected $_parent;
  protected $_compiler;
  protected $_lineNumber;

  public function __construct()
  {
    $this->_children = array();
    $this->_indentation = -1;
    $this->_childrenCount = 0;
    $this->_lineNumber = 0;
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

  public function setLineNumber($lineNumber)
  {
    $this->_lineNumber = $lineNumber;
  }

  public function getLineNumber()
  {
    return $this->_lineNumber;
  }

  public function setCompiler(Compiler $compiler)
  {
    $this->_compiler = $compiler;
  }

  public function getCompiler()
  {
    return $this->_compiler;
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
  	// @todo add some doc here explaining why this
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

  public function getChildrenCount()
  {
    return $this->_childrenCount;
  }

  public function hasChildren()
  {
    return $this->_childrenCount > 0;
  }

  public function renderChildren()
  {
    $output = '';

    for ($i = 0; $i < $this->_childrenCount; ++$i) {
      $res = $this->_children[$i]->render();
      $output .= $res;
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
