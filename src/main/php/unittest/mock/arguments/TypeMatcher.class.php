<?php namespace unittest\mock\arguments;

use lang\Type;
use lang\reflect\InvocationHandler;

/**
 * Argument matcher based on argument type
 *
 * @test  xp://unittest.mock.tests.TypeMatcherTest
 */
class TypeMatcher implements IArgumentMatcher, InvocationHandler {
  private 
    $type,
    $matchNull;
  
  /**
   * Constructor.
   * 
   * @param   string type
   * @param   bool matchNull default TRUE
   */
  public function __construct($type, $matchNull= true) {
    $this->type= Type::forName($type);
    $this->matchNull= $matchNull;
  }
  
  /**
   * Matches implementation
   * 
   * @param   var value
   * @return  bool
   */
  public function matches($value) {
    if (null === $value && $this->matchNull) {
      return true;
    }
    
    return $this->type->equals(typeof($value));
  }

  /**
   * Invocation handler
   *
   * @param   lang.reflect.Proxy
   * @param   string method
   * @param   var[] args
   * @return  var
   */
  public function invoke($proxy, $method, $args) {
    if ('matches' === $method) {
      return $this->matches($args[0]);
    }
    
    throw new \lang\IllegalStateException('Unknown method "'.$method.'".');
  }
}