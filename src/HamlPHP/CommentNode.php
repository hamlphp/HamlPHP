<?php

class CommentNode extends HamlNode
{
  const HTML_COMMENT = '/';
  const HAML_COMMENT = '-#';
  const HTML_COMMENT_TYPE = 1;
  const HAML_COMMENT_TYPE = 2;

  private $_commentType = null;

  public function __construct($line)
  {
    parent::__construct($line);
    $this->identifyCommentType();
  }

  private function identifyCommentType()
  {
    if (substr($this->getHaml(), 0, 1) === CommentNode::HTML_COMMENT) {
      $this->_commentType = CommentNode::HTML_COMMENT_TYPE;
    } else if (substr($this->getHaml(), 0, 2) === CommentNode::HAML_COMMENT) {
      $this->_commentType = CommentNode::HAML_COMMENT_TYPE;
    }
  }

  public function render()
  {
    switch ($this->_commentType) {
      case CommentNode::HTML_COMMENT_TYPE:
        return $this->renderHtmlComment();
      case CommentNode::HAML_COMMENT_TYPE:
        return $this->renderHamlComment();
      default:
        throw new Exception("Invalid comment type");
    }
  }

  private function renderHtmlComment()
  {
    $output = $this->getSpaces() . "<!--";

    if ($this->hasChildren()) {
      $output .= "\n" . $this->renderChildren() . $this->getSpaces();
    } else {
      $output .= substr($this->getHaml(), 1) . " ";
    }

    $output .= "-->";

    return $output;
  }

  private function renderHamlComment()
  {
    return '';
  }
}