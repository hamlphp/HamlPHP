<?php

class FileStorage implements Storage, ContentEvaluator
{
  protected $_path = null;
  protected $_extension = null;

  public function __construct($cachePath, $extension = '.cached.php')
  {
    $this->_path = $cachePath;
    $this->_extension = $extension;
  }

  public function evaluate($content, array $contentVariables = array(), $id = null)
  {
    if (null === $id) {
      throw new Exception("FileStorage: Could not evaluate. ID is null.");
    }

    ob_start();
    extract($contentVariables);
    require $this->_path . $id . $this->_extension;
    $result = ob_get_clean();

    return $result;
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
  public function fetch($id)
  {
    return file_get_contents($this->_path . $id . $this->_extension);
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
