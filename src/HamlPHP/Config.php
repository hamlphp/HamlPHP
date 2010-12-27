<?php

require_once 'Storage/Storage.php';

class Config
{
  private $_cacheEnabled = true;

  public function enableCache($boolean)
  {
    $this->_cacheEnabled = (boolean) $boolean;
  }

  public function isCacheEnabled()
  {
    return $this->_cacheEnabled;
  }
}