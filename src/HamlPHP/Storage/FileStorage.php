<?php

class FileStorage implements Storage
{
  protected $_path = null;
  protected $_extension = null;

  public function __construct($cachePath, $extension = '.cached.php')
  {
    $this->_path = $cachePath;
    $this->_extension = $extension;
  }

  /**
   * (non-PHPdoc)
   * @see Storage::isFresh()
   */
  public function isFresh($id)
  {
    $path = $this->_path . $id . $this->_extension;

    return file_exists($id)
        && file_exists($path)
        && filemtime($id) < filemtime($path);
  }

  /**
   * (non-PHPdoc)
   * @see Storage::cache()
   */
  public function cache($id, $content)
  {
    $path = $this->_path . $id . $this->_extension;

    if (!file_exists($path)) {
      @fopen($path, "x+");
    }

    file_put_contents($path, $content);
  }

  /**
   * (non-PHPdoc)
   * @see Storage::fetch()
   */
  public function fetch($id, array $templateVars = array())
  {
    ob_start();
    extract($templateVars);
    require $this->_path . $id . $this->_extension;
    $result = ob_get_clean();

    return $result;
  }

  /**
   * (non-PHPdoc)
   * @see Storage::remove()
   */
  public function remove($id)
  {
    $path = $this->_path . $id . $this->_extension;
    return unlink($path);
  }
}
