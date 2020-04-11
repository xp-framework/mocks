<?php namespace unittest\mock\tests;

/**
 * An abstract class for tests
 */
abstract class PartiallyImplementedAbstractDummy implements IComplexInterface {

  /**
   * Static method
   *
   * @param   var $param1
   * @return  var
   */
  public static function aStaticFunction($param1) { 
    return $param1; 
  }

  /**
   * Constructor
   *
   * @param   var $param1
   * @param   string $param2
   */
  public function __construct($param1, $param2= 'default') {
    // Empty
  }

  /**
   * An implementation of the foo method from IComplexInterface.
   * 
   * @return string
   */
  public function foo()  {
    return 'IComplexInterface.foo';
  }

  /**
   * Abstract method introduced in this class.
   *
   * @param  var $a
   */
  public abstract function baz($a);

  /**
   * Some non-interface method.
   *
   * @return string
   */
  public function foobar() {
    return 'PartiallyImplementedAbstractDummy.foobar';
  }
}