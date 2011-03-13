<?php

require_once 'test_helper.php';
require_once 'src/HamlPHP/HamlNode.php';

class HamlNodeTest extends PHPUnit_Framework_TestCase
{
  public function testSetPositiveIndentationLevel()
  {
    $parentNode = new HamlNode("parent");
    $childNode = new HamlNode("  child");
    $parentNode->addNode($childNode);

    $this->assertEquals(0, $parentNode->getIndentationLevel());
    $this->assertEquals(2, $childNode->getIndentationLevel());

    $parentNode->setIndentationLevel(
        $parentNode->getIndentationLevel() + 2);

    $this->assertEquals(2, $parentNode->getIndentationLevel());
    $this->assertEquals(4, $childNode->getIndentationLevel());
  }

  public function testSetNegativeIndentationLevel()
  {
    $parentNode = new HamlNode("  parent");
    $childNode = new HamlNode("    child");
    $childNode2 = new HamlNode("      child");
    $parentNode->addNode($childNode);
    $childNode->addNode($childNode2);

    $this->assertEquals(2, $parentNode->getIndentationLevel());
    $this->assertEquals(4, $childNode->getIndentationLevel());
    $this->assertEquals(6, $childNode2->getIndentationLevel());

    $parentNode->setIndentationLevel(
        $parentNode->getIndentationLevel() - 2);

    $this->assertEquals(0, $parentNode->getIndentationLevel());
    $this->assertEquals(2, $childNode->getIndentationLevel());
    $this->assertEquals(4, $childNode2->getIndentationLevel());
  }

  public function testSetIndentationLevelTemplate()
  {
    $parentNode = new HamlNode("parent");
    $childNode = new HamlNode("  child");
    $childNode2 = new HamlNode("    child");
    $parentNode->addNode($childNode);
    $childNode->addNode($childNode2);

    $parentNode->setIndentationLevel(
        $parentNode->getIndentationLevel() + 2); 

    $output = $parentNode->render();

    $this->assertEquals("  parent\n    child\n      child\n", $output);
  }
}
