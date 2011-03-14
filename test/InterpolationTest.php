<?php

require_once 'test_helper.php';
require_once 'src/HamlPHP/Interpolation.php';

class InterpolationTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = new Compiler();
  }

  public function testInterpolation()
  {
    $interpolation = new Interpolation("#{1 + 1}");
    $this->assertEquals("<?php echo 1 + 1; ?>", $interpolation->render());

    $interpolation = new Interpolation("test #{'hello'} #{'world'}.");
    $this->assertEquals(
        "test <?php echo 'hello'; ?> <?php echo 'world'; ?>.", $interpolation->render());

    $interpolation = new Interpolation("test #{'hello #{world'}.");
    $this->assertEquals(
        "test <?php echo 'hello #{world'; ?>.", $interpolation->render());

    $interpolation = new Interpolation("#{}");
    $this->assertEquals("#{}", $interpolation->render());
  }

  public function testInterpolationTemplate()
  {
    $actual = $this->compiler->parseFile(template('interpolation.haml'));
    $this->assertEquals(contents('interpolation_expected.html'), $actual);
  }
}
