<?php

require_once 'BaseTestCase.php';
require_once 'BaseTestCase.php';

class ElementNodeTest extends BaseTestCase 
{   
    public function testExplicitlyClosingElement() {
        $code = "%img{:src => 'my_image.jpg'}/";
        $result = trim($this->compiler->parseString($code));
        
        if(empty($result))
        	$this->fail("Compilation result is empty");
        
        $this->compareXmlStrings("<img src='my_image.jpg' />", $result);
    }
    
    public function testElementsWithTemplate() {
        $actual = $this->compiler->parseFile( $this->getTemplatePath('elements'));
        //$this->assertEquals( $this->getExpectedResult('elements'), $actual);
        
    }
}

?>
