<?php namespace unittest\mock\tests;

/**
 * An abstract dummy class for testing.
 */
abstract class AbstractDummy {

  /**
   * A concrete method
   *
   * @return  string
   */
  public function concreteMethod() {
    return 'concreteMethod';
  }

  /**
   * An abstract method
   */
  public abstract function abstractMethod();
  
  /**
   * Returns whether a given value is equal to this class
   *
   * @param   var $cmp
   * @return  bool
   */
  public function equals($cmp) {
    return true;
  }
}
