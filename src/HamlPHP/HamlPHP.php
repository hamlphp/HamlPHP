<?php

require_once 'Config.php';
require_once 'Compiler.php';

class HamlPHP
{
  const CACHED_EXTENSION = '.cached.php';

  private $_compiler = null;
  private $_config = null;

  public function __construct(Compiler $compiler = null, Config $config = null)
  {
    $this->_compiler = $compiler !== null ? $compiler : new Compiler();
    $this->_config = $config !== null ? $config : new Config();
  }

  public function getConfiguration()
  {
    return $this->_config;
  }

  public function getCompiler()
  {
    return $this->_compiler;
  }

  /**
   * Parses a haml file and returns a cached path to the file.
   * 
   * @param string $fileName
   */
  public function parseFile($fileName)
  {
    if ($this->_config->isCacheEnabled() && !$this->shouldCache($fileName)) {
      return $this->getCachedPath($fileName);
    }
    return $this->cache($this->_compiler->parseFile($fileName), $fileName);
  }

  public function parseString($string)
  {
    
  }

  /**
   * Returns true if a file is already cached.
   * 
   * @param string $fileName
   */
  private function isCached($fileName)
  {
    return file_exists($this->getCachedPath($fileName));
  }

  private function getCachedPath($fileName)
  {
    return $this->_config->getCacheDir() . '/' . $fileName . HamlPHP::CACHED_EXTENSION;
  }

  /**
   * Returns true if a file should be cached.
   * 
   * @param string $fileName
   */
  private function shouldCache($fileName)
  {
    if (!$this->isCached($fileName)) {
      return true;
    }

    $cachedFileTime = filemtime($this->getCachedPath($fileName));
    return filemtime($fileName) > $cachedFileTime;
  }

  /**
   * Writes an output to a file.
   * 
   * @param string $output
   * @param string $fileName
   */
  private function cache($output, $fileName)
  {
    $path = $this->getCachedPath($fileName);

    if (!file_exists($path)) {
      @fopen($path, "x+");
    }
    
    file_put_contents($path, $output);
    return $path;
  }
}