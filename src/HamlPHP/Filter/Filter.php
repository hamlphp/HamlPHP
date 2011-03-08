<?php

interface Filter
{
  /**
   * Returns an identifier of this filter.
   * 
   * @return string
   */
  public function getIdentifier();

  /**
   * Filters given text.
   * 
   * @param string $text
   */
  public function filter($text);

  /**
   * Sets a filter node.
   */
  public function setFilterNode($node);
}
