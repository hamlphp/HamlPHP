<?php

require_once 'test_helper.php';

class ElementNodeTest extends PHPUnit_Framework_TestCase {
    private $compiler;
    
    public function setUp() {
        $this->compiler = getTestCompiler();
    }
    
    public function testExplicitlyClosingElement() {
        $code = "%img{:src => 'my_image.jpg'}/";
        $result = $this->compiler->parseString($code);
        $this->assertEquals("<img src='my_image.jpg' />\n", $result);
    }
    
    public function testElementsWithTemplate() {
        $actual = $this->compiler->parseFile(template_path('elements'));
        $this->assertEquals(expected_result('elements'), $actual);
    }
}

?>
