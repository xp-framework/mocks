<?php namespace unittest\mock\tests;

/**
 * Interface with overloaded methods
 *
 * @see   xp://lang.reflect.Proxy
 */
interface OverloadedInterface {
  
  /**
   * Overloaded method.
   *
   */
  #[@overloaded(signatures= [
  #  ['string'],
  #  ['string', 'string']
  #])]
  public function overloaded();
}
