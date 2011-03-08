<?php

require_once 'RootNode.php';
require_once 'HamlNode.php';
require_once 'Element.php';

class ElementNode extends HamlNode
{
  private $_phpVariable;

  public function __construct($line)
  {
    parent::__construct($line);
    $this->_phpVariable = false;
  }

  public function render()
  {
    $element = new Element($this->getHaml());
    $this->_phpVariable = $element->isPhpVariable();
    return $this->renderHtml($element);
  }

  private function renderHtml(Element $element)
  {
    $output = '';

    if ($this->getIndentationLevel() > 0) {
      $output .= $this->getSpaces() . '<' . $element->getTag();
    } else {
      $output .= '<' . $element->getTag();
    }

    if (($id = $element->getId()) !== null) {
      $output .= ' id="' . $id . '"';
    }

    if (($classes = $element->getClasses()) !== null) {
      $output .= ' class="' . $classes . '"';
    }

    if (($attributes = $element->getAttributes()) !== null) {
      $attrs = array_keys($attributes);
      $i = count($attrs);
      while ($i--) {
        $output .= ' ' . $attrs[$i] . '="' . $attributes[$attrs[$i]] . '"';
      }
    }

    $content = $this->renderTagContent($element->getInlineContent());

    // render inline content
    $output .= '>' . $content . '</' . $element->getTag() . '>';
    return $output . "\n";
  }

  private function renderTagContent($content)
  {
    if ($this->hasChildren()) {
      $content = "\n" . $this->renderChildren() . $this->getSpaces();
    }

    if ($content === null) {
      $content = '';
    }

    if ($this->_phpVariable) {
      $content = "<?php echo " . $content . " ?>";
    }

    return $content;
  }
}
