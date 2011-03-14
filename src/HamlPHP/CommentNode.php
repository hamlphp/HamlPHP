<?php

class CommentNode extends HamlNode
{
  const HTML_COMMENT = '/';
  const HAML_COMMENT = '-#';
  const CONDITIONAL_COMMENT = "/\/\[([^\\]]+)\]/";
  const HTML_COMMENT_TYPE = 1;
  const HAML_COMMENT_TYPE = 2;
  const CONDITIONAL_COMMENT_TYPE = 3;

  private $_commentType = null;
  private $_conditionalMatches = array();

  public function __construct($line)
  {
    parent::__construct($line);
    $this->identifyCommentType();
  }

  private function identifyCommentType()
  {
    $haml = $this->getHaml();

    if (preg_match(
          CommentNode::CONDITIONAL_COMMENT, $haml, $this->_conditionalMatches)) {
      $this->_commentType = CommentNode::CONDITIONAL_COMMENT_TYPE;

    } else if (substr($haml, 0, 1) === CommentNode::HTML_COMMENT) {
      $this->_commentType = CommentNode::HTML_COMMENT_TYPE;

    } else if (substr($haml, 0, 2) === CommentNode::HAML_COMMENT) {
      $this->_commentType = CommentNode::HAML_COMMENT_TYPE;
    }
  }

  public function render()
  {
    $output = "";

    switch ($this->_commentType) {
      case CommentNode::HTML_COMMENT_TYPE:
        $output = $this->renderHtmlComment() . "\n";
        break;
      case CommentNode::HAML_COMMENT_TYPE:
        $output = $this->renderHamlComment() . "\n";
        break;
      case CommentNode::CONDITIONAL_COMMENT_TYPE:
        $output = $this->renderConditionalComment() . "\n";
        break;
      default:
        throw new Exception("Invalid comment type: " . $this->getHaml());
    }

    $interpolation = new Interpolation($output);
    return $interpolation->render();
  }

  private function renderHtmlComment()
  {
    $output = $this->getSpaces() . "<!--";
    $output .= $this->renderHtmlCommentBody();
    $output .= "-->";

    return $output;
  }

  private function renderHtmlCommentBody()
  {
    if ($this->hasChildren()) {
      return "\n" . $this->renderChildren() . $this->getSpaces();
    } else {
      return substr($this->getHaml(), 1) . " ";
    }
  }

  private function renderConditionalComment()
  {
    $output =
        $this->getSpaces() . "<!--[" . $this->_conditionalMatches[1] . "]>";

    $output .= $this->renderHtmlCommentBody();
    $output .= "<![endif]-->";

    return $output;
  }

  private function renderHamlComment()
  {
    return '';
  }
}