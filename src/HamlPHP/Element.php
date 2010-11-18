<?php

class Element
{
  const HAML_REGEXP = '/^(?P<tag>%\w+)?(?P<id>#\w*)?(?P<classes>\.[\w\.\-]*)*(?P<attributes>\((.*)\))?(?P<php>=)?(?P<inline>[^\w\.#\{].*)?$/';

  const ELEMENT = '%';
  const ID = '#';
  const KLASS = '.';

  private $_haml = null;
  private $_tag = null;
  private $_id = null;
  private $_classes = null;
  private $_inlineContent = null;
  private $_php = false;
  private $_attributes = null;
 
  public function __construct($line)
  {
    $this->_haml = $line;
    $this->parseHaml();
  }

  private function parseHaml()
  {
    preg_match(Element::HAML_REGEXP, $this->_haml, $splitTags);

    $this->_tag = isset($splitTags['tag']) && $splitTags['tag'] !== ''
        ? trim($splitTags['tag'], Element::ELEMENT) : 'div';

    $this->_inlineContent = isset($splitTags['inline'])
        ? trim($splitTags['inline']) : '';

    $this->_id = isset($splitTags['id']) && $splitTags['id'] !== ''
        ? $this->parseId(trim($splitTags['id'])) : null;

    $this->_classes = isset($splitTags['classes']) && $splitTags['classes'] !== ''
        ? $this->parseClasses(trim($splitTags['classes'])) : null;

    $this->_php = isset($splitTags['php']) && $splitTags['php'] !== '';

    $this->_attributes = isset($splitTags['attributes']) && $splitTags['attributes'] !== ''
        ? $this->parseAttributes($splitTags[5]) : null;
  }

  private function parseClasses($classesString)
  {
    return trim(implode(" ", explode(".", $classesString)));
  }

  private function parseAttributes($attrs)
  {
    $attrs = explode(",", trim($attrs));
    $len = count($attrs);
    $parsedAttrs = array();

    while ($len--) {
      $attr = trim($attrs[$len]);

      if (strpos($attr, "=>") === false) {
        throw new Exception('Invalid element attribute');
      }

      list($attr, $value) = explode("=>", $attr);
      $attr = $this->removeQuotes($attr);
      $value = $this->removeQuotes($value);

      $parsedAttrs[$attr] = $value;
    }

    return $parsedAttrs;
  }

  private function removeQuotes($str)
  {
    $str = str_replace("'", "", trim($str));
    return str_replace("\"", "", $str);
  }

  private function parseId($id)
  {
    return str_replace("#", "", $id);
  }

  public function getTag()
  {
    return $this->_tag;
  }

  public function getId()
  {
    return $this->_id;
  }

  public function isPhpVariable()
  {
    return $this->_php;
  }

  public function getClasses()
  {
    return $this->_classes;
  }

  public function getAttributes()
  {
    return $this->_attributes;
  }

  public function getInlineContent()
  {
    return $this->_inlineContent;
  }
}