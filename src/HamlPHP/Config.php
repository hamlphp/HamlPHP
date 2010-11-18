<?php

class Config
{
  private $_cacheDir = null;
  private $_cacheEnabled = true;

  public function __construct()
  {
    $this->_cacheDir = dirname(__FILE__) . '/../../tmp';
  }

  public function enableCache($boolean)
  {
    $this->_cacheEnabled = (boolean) $boolean;
  }

  public function isCacheEnabled()
  {
    return $this->_cacheEnabled;
  }

  public function setCacheDir($dir)
  {
    $this->_cacheDir;
  }

  public function getCacheDir()
  {
    return $this->_cacheDir;
  }
}