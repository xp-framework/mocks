<?php namespace unittest\mock\tests;

/**
 * Dummy interface used in other tests
 */
interface IComplexInterface extends IEmptyInterface {

  /**
   * Foo
   */
  public function foo();

  /**
   * Bar
   *
   * @param   var $a
   * @param   var $b
   */
  public function bar($a, $b);
  
  /**
   * Foo get accessor
   *
   * @return  var
   */
  public function getFoo();

  /**
   * Foo set accessor
   *
   * @param   var $value
   * @return  void
   */
  public function setFoo($value);
  
  /**
   * Foo with type hint
   *
   * @param   net.xp_framework.unittest.tests.mock.IEmptyInterface $arg
   */
  public function fooWithTypeHint(IEmptyInterface $arg);
}
