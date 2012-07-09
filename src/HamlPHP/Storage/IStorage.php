<?php

interface IStorage
{
  /**
   * Returns true if content is fresh and false if content should be cached.
   */
  public function isFresh($id);

  /**
   * Cache contents and index it under the specified id.
   */
  public function cache($id, $content);

  /**
   * Returns content from a cache.
   */
  public function fetch($id);

  /**
   * Remove cached content.
   */
  public function remove($id);
  
  /**
   * The content to generate the id for.
   * 
   * @param string $content
   */
  public function generateContentId($content);
}