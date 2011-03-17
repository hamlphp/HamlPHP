<?php

require_once 'Interpolation.php';

class DoctypeNode extends HamlNode
{
  const XHTML10_T = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
  const XHTML10_S = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
  const XHTML10_F = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
  const HTML5 = '<!DOCTYPE html>';

  private $_type;
  
  public function __construct($line)
  {
    parent::__construct($line);
    $parts = explode(' ', trim($line));
    $this->_type = isset($parts[1]) ? $parts[1] : '!!!';
  }

  public function render()
  {
    $interpolation = new Interpolation($this->renderDoctype() . "\n");
    return $interpolation->render();
  }

  private function renderDoctype()
  {
    if ($this->_type === '!!!') {
      return DoctypeNode::XHTML10_T;
    }

    if ($this->_type === 'Strict') {
      return DoctypeNode::XHTML10_S;
    }

    if ($this->_type === 'Frameset') {
      return DoctypeNode::XHTML10_F;
    }

    if ($this->_type === '5') {
      return DoctypeNode::HTML5;
    }

    return '';
  }
}