<?php

class Interpolation
{
  const REGEXP = '/(?:\#\{([^\}]+)\})+/';
  const REPLACEMENT = '<?php echo $1; ?>';

  private $_text = null;

  public function __construct($text)
  {
    $this->_text = $text;
  }

  public function render()
  {
    return preg_replace(self::REGEXP, self::REPLACEMENT, $this->_text);
  }
}
