<?php

interface IHamlFilter
{
  /**
   * Returns an identifier of this filter.
   * 
   * @return string
   */
  public function getIdentifier();

  /**
   * Filters given node.
   * 
   * @param HamlNode object
   */
  public function filter(HamlNode $node);
}
