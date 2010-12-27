<?php

require_once 'Config.php';
require_once 'Compiler.php';

class HamlPHP
{
  const CACHED_EXTENSION = '.cached.php';

  private $_compiler = null;
  private $_config = null;
  private $_storage = null;

  public function __construct(
      Storage $storage = null, Compiler $compiler = null,
      Config $config = null)
  {
    $this->_compiler = $compiler !== null ? $compiler : new Compiler();
    $this->_config = $config !== null ? $config : new Config();
    $this->_storage = $storage;
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
  public function parseFile($fileName, array $templateVars = array())
  {
    if ($this->_storage === null) {
      throw new Exception('Storage not set');
    }

    if ($this->_config->isCacheEnabled()
        && $this->_storage->isFresh($fileName)) {
      return $this->_storage->fetch($fileName, $templateVars);
    }

    // file is not fresh, so cache it
    $this->_storage->cache($fileName, $this->_compiler->parseFile($fileName));
    return $this->_storage->fetch($fileName, $templateVars);
  }
}