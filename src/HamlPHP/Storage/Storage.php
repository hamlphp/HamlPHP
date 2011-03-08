<?php

interface Storage
{
  /**
   * Returns true if content is fresh and false if content should be cached.
   */
  public function isFresh($id);

  /**
   * Cache contents of a file.
   */
  public function cache($id, $content);

  /**
   * Returns content from a cache
   */
  public function fetch($id);

  /**
   * Remove cached content.
   */
  public function remove($id);
}