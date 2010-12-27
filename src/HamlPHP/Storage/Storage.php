<?php

interface Storage
{
  /**
   * Returns true if a file is fresh and false if a file should be cached.
   */
  public function isFresh($id);

  /**
   * Cache contents of a file.
   */
  public function cache($id, $content);

  /**
   * Returns content from a cache
   */
  public function fetch($id, array $templateVariables);

  /**
   * Remove cached content.
   */
  public function remove($id);
}