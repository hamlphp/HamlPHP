<?php

require_once 'Config.php';
require_once 'Compiler.php';
require_once 'ContentEvaluator/DefaultContentEvaluator.php';

class HamlPHP
{
  private $_compiler = null;
  private $_config = null;
  private $_storage = null;
  private $_contentEvaluator = null;

  public function __construct(Storage $storage, Config $config = null)
  {
    $this->_compiler = new Compiler();
    $this->_config = $config !== null ? $config : new Config();
    $this->_storage = $storage;

    if ($this->_storage instanceof ContentEvaluator) {
      $this->setContentEvaluator($this->_storage);
    } else {
      $this->setContentEvaluator(new DefaultContentEvaluator());
    }
  }

  public function getConfiguration()
  {
    return $this->_config;
  }

  public function setContentEvaluator(ContentEvaluator $contentEvaluator)
  {
    $this->_contentEvaluator = $contentEvaluator;
  }

  public function setCompiler(Compiler $compiler)
  {
    $this->_compiler = $compiler;
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
    $content = $this->getContentFromStorage($fileName);
    return $this->_contentEvaluator->evaluate($content, $templateVars, $this->generateFileId($fileName));
  }

  /**
   * Returns content from a storage
   * 
   * @param string $fileName
   * @return string
   */
  public function getContentFromStorage($fileName)
  {
  	$fileId = $this->generateFileId($fileName);
  	
    if ($this->_storage === null) {
        throw new Exception('Storage not set');
    }

    if ($this->_config->isCacheEnabled()
        && $this->_storage->isFresh($fileId)) {
      return $this->_storage->fetch($fileId);
    }

    // file is not fresh, so compile and cache it
    $this->_storage->cache($fileId, $this->_compiler->parseFile($fileName));
    return $this->_storage->fetch($fileId);
  }
  
  private function generateFileId($filename)
  {
  	return str_replace(array(':','/','\\'), '_', ltrim($filename, '/\\'));
  }
}
