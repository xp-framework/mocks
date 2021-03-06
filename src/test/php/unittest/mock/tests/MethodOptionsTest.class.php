<?php namespace unittest\mock\tests;
 
use lang\{IllegalArgumentException, IllegalStateException};
use unittest\mock\MethodOptions;
use unittest\{Expect, Test};

/**
 * Tests for the MethodOptions class
 *
 * @see   xp://unittest.mock.MethodOptions
 */
class MethodOptionsTest extends \unittest\TestCase {
  private $sut= null;

  /**
   * Creates the fixture
   */
  public function setUp() {
    $this->sut= new MethodOptions(new \unittest\mock\Expectation('method'), 'method');
  }
    
  #[Test, Expect(IllegalArgumentException::class)]
  public function expectationRequiredOnCreate() {
    new MethodOptions(null, null);
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function nameRequiredOnCreate() {
    new MethodOptions(new \unittest\mock\Expectation('method'), null);
  }
  
  #[Test]
  public function canCallReturns() {
    $this->sut->returns(null);
  }

  #[Test]
  public function returns_valueSetInExpectation() {
    $expectation= new \unittest\mock\Expectation('foo');
    $sut= new MethodOptions($expectation, 'foo');
    $expected= new Value();
    $sut->returns($expected);
    $this->assertEquals($expected, $expectation->getReturn());
  }

  #[Test]
  public function setPropertyBehavior_sets_expectation_to_prop_behavior() {
    $expectation=new \unittest\mock\Expectation('setFoo');
    $sut= new MethodOptions($expectation, 'setFoo');
    $sut->propertyBehavior();
    $this->assertTrue($expectation->isInPropertyBehavior());
  }
  
  #[Test]
  public function throws_sets_the_exception_property_of_the_expectation() {
    $expectation= new \unittest\mock\Expectation('foo');
    $sut= new MethodOptions($expectation, 'foo');
    $expected= new \lang\XPException('foo');
    $sut->throws($expected);
    $this->assertEquals($expected, $expectation->getException());
  }
  
  #[Test, Expect(IllegalStateException::class)]
  public function setPropertyBehavior_throws_an_exception_if_no_setter_or_getter() {
    $expectation= new \unittest\mock\Expectation('blabla');
    $sut= new MethodOptions($expectation, 'blabla');
    $sut->propertyBehavior();
  }
}