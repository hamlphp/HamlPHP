<?php

class InvalidTagException extends Exception
{
  public function __construct($msg)
  {
    parent::__construct($msg);
  }
}

class TagNode extends HamlNode
{
  private $_tags = array(
    'for' => 'endfor',
    'if' => 'endif',
    'while' => 'endwhile',
    'foreach' => 'endforeach',
  );

  private $_line = null;

  const TAG_PATTERN = '/-\s*((\w+)\s*([^\:]+))(:)?/';

  public function __construct($line)
  {
    parent::__construct($line);
    $this->_line = $line;
  }

  public function render()
  {
     if (!preg_match(TagNode::TAG_PATTERN, $this->_line, $matches)) {
       throw new InvalidTagException('Tag does not match the pattern');
     }

     if (!array_key_exists($matches[2], $this->_tags)) {
       throw new InvalidTagException('Invalid control structure ' . $matches[2]);
     }

     $line = $matches[1];
     $line .= isset($matches[4]) ? $matches[4] : ':';

     return $this->generateTagContent($line, $matches[2]) . "\n";
  }

  private function generateTagContent($line, $type)
  {
    $content = $this->getSpaces() . "<?php " . $line . " ?>";
    $content .= "\n" . $this->renderChildren() . $this->getSpaces();
    $content .= "<?php " . $this->_tags[$type] . "; ?>";
    return $content;
  }
}