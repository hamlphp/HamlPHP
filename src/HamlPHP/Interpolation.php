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

  /**
   * @todo Refactor!
   * @throws LogicException
   */
  public function render()
  {
    $interpolationStarted = false;
    $content = "";
    $interpolationContent = "";
    $len = strlen($this->_text);
    $i = 0;

    while ($i < $len) {
      $currentChar = $this->_text[$i];
      $nextChar = ($i + 1 <= $len - 1) ? $this->_text[$i + 1] : null;

      if ($interpolationStarted) {
        if ($currentChar === '}') {
          $interpolationStarted = false;

          if (empty($interpolationContent)) {
            throw new LogicException("Empty interpolation: " . $this->_text);
          }

          $content .= "<?php echo " . $interpolationContent . "; ?>";
          $interpolationContent = '';
          $i++;
          continue;
        }

        if (null !== $nextChar && '#{' === $currentChar . $nextChar) {
          throw new LogicException(
              "Nested interpolation not allowed: " . $this->_text);
        }

        $interpolationContent .= $currentChar;
        $i++;
        continue;
      }

      if (null !== $nextChar && '#{' === $currentChar . $nextChar) {
        $interpolationStarted = true;
        $i += 2;
        continue;
      }

      $content .= $currentChar;
      $i++;
    }

    if ($interpolationStarted) {
      throw new LogicException("Unclosed interpolation: " . $this->_text);
    }

    return $content;
  }
}
