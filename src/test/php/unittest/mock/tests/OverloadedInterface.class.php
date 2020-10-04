<?php namespace unittest\mock\tests;

use lang\Overloaded;

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
  #[Overloaded(signatures: [['string'], ['string', 'string']])]
  public function overloaded();
}