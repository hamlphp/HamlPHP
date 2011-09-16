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
    'else if' => 'endif',
    'else' => 'endif',
    'while' => 'endwhile',
    'foreach' => 'endforeach',
  );

  private $_line = null;
  
  private $_isTag = false;
  
  /**
   * Can be either TagNode::SILENT_MODE or TagNode::LOUD_MODE
   * @var string
   */
  private $_mode;
  
  /**
   * The tag this node represents. One of $_tags.
   * @var string
   */
  private $_tag;
  
  /**
   * The php code whether it's a tag or a script
   * @var string
   */
  private $_code;

  const CODE_PATTERN = '/(?P<mode>[-=])\s*(?P<code>(?P<tag>[^\:\(\)]+)\s*(\(.+\))?(?P<colon>:)?\s*$|[^\r\n]+)/';
  const SILENT_MODE = '-';
  const LOUD_MODE = '=';
  
  public function __construct($line)
  {
    parent::__construct($line);
    $this->_line = $line;
    
    if (!preg_match(TagNode::CODE_PATTERN, $this->_line, $matches)) {
      throw new InvalidTagException('Line does not match the pattern');
    }
    
    $this->_mode = $matches['mode'];
    $this->_code = $matches['code'];
    
    if (isset($matches['tag'])) {
      $tag = trim($matches['tag']);

      if (isset($this->_tags[$tag])) {
        $this->_isTag = true;
        $this->_tag = $tag;
        if (!isset($matches['colon']))
          $this->_code .= ':';
	    }
    }

    if($this->_isTag && TagNode::LOUD_MODE == $this->_mode)
    	throw new InvalidTagException('Loud mode is not allowed for the tags '.join(', ', array_keys($this->_tags)).
    		'. Use silent mode (-).');
  }

  public function render()
  {
  	if($this->_isTag)
      return $this->generateTagContent() . "\n";
    
    $mode = '';
    if(TagNode::LOUD_MODE == $this->_mode)
    	$mode = 'echo ';
    
    return $this->getSpaces() . "<?php $mode{$this->_code} ?>\n";
  }

  public function getTagName()
  {
    return $this->_tag;
  }

  private function isTag($line)
  {
    if (preg_match(TagNode::CODE_PATTERN, $line, $matches)) {
      return true;
    }

    return false;
  }

  private function generateTagContent()
  {
    $content = $this->getSpaces() . "<?php " . $this->_code . " ?>\n";
    $content .= $this->renderChildren();

    $compiler = $this->getCompiler();
    $nextLine = $compiler->getLine($this->getLineNumber() + $this->getChildrenCount() + 1);
    $nextLineTag = null;

    if ($nextLine !== null && $this->isTag($nextLine)) {
      $nextLineTag = new TagNode($nextLine);
    }

    if (!($nextLineTag !== null
        && strtolower($nextLineTag->getTagName()) !== 'if'
        && strlen($nextLineTag->getSpaces()) == strlen($this->getSpaces()))) {
      $content .= $this->getSpaces() . "<?php " . $this->_tags[$this->_tag] . "; ?>";
    } else {
      $content = rtrim($content);
    }

    return $content;
  }
}
