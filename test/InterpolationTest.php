<?php

require_once 'test_helper.php';
require_once '../src/HamlPHP/Lang/Interpolation.php';

class InterpolationTest extends PHPUnit_Framework_TestCase
{
  protected $compiler = null;

  public function __construct()
  {
    $this->compiler = getTestCompiler();
  }

  public function testInterpolation()
  {
    $interpolation = new Interpolation("#{1 + 1}");
    $this->assertEquals("<?php echo 1 + 1; ?>", $interpolation->render());

    $interpolation = new Interpolation("test #{'hello'} #{'world'}.");
    $this->assertEquals(
        "test <?php echo 'hello'; ?> <?php echo 'world'; ?>.", $interpolation->render());
  }

  /**
   * @expectedException LogicException
   */
  public function testEmptyInterpolationThrows()
  {
    $interpolation = new Interpolation("#{}");
    $interpolation->render();
  }

  public function testUnclosedInterpolationThrows()
  {
    try {
      $interpolation = new Interpolation("#{1 + 1");
      $interpolation->render();

      // should not get here
      $this->fail("testUnclosedInterpolationThrows: did not throw");
    } catch (LogicException $e) {
      // do nothing
    }

    try {
      $interpolation = new Interpolation("test #{'jei");
      $interpolation->render();

      // should not get here
      $this->fail("testUnclosedInterpolationThrows: did not throw");
    } catch (LogicException $e) {
      // do nothing
    }
  }

  /**
   * @expectedException LogicException
   */
  public function testNestedInterpolationThrows()
  {
    $interpolation = new Interpolation("test #{1 + 1 #{2 + 2}}");
    $interpolation->render();
  }

  public function testInterpolationTemplate()
  {
    $actual = $this->compiler->parseFile(template('interpolation.haml'));
    $this->assertEquals(contents('interpolation_expected.html'), $actual);
  }
}
