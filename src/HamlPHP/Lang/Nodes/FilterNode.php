<?php

class FilterNode extends HamlNode
{
  private $_filterContainer = null;

  public function __construct($line, FilterContainer $container = null)
  {
    parent::__construct($line);
    $this->_filterContainer = $container;
  }

  public function render()
  {
    if (null === $this->_filterContainer) {
      return '';
    }

    $identifier = str_replace(':', '', $this->getHaml());
    $filter = $this->_filterContainer->getFilter($identifier);

    if (null === $filter) {
      throw new Exception(sprintf("Unknown filter '%s'.", $identifier));
    }

    $interpolation = new Interpolation($filter->filter($this));
    return $interpolation->render();
  }
}