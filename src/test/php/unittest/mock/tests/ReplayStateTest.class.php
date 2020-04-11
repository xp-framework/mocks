<?php namespace unittest\mock\tests;
 
use lang\{Error, IllegalArgumentException};
use unittest\actions\RuntimeVersion;
use unittest\mock\{Expectation, ExpectationList, ReplayState};
use util\collections\HashTable;

/**
 * Testcase for ReplayState
 *
 * @see   xp://unittest.mock.ReplayState
 */
class ReplayStateTest extends \unittest\TestCase {
  private 
    $sut            = null,
    $expectationMap = null,
    $properties     = null;
  
  /**
   * Creates the fixture
   */
  public function setUp() {
    $this->expectationMap= new HashTable();
    $this->properties= new HashTable();
    $this->sut= new ReplayState($this->expectationMap, $this->properties);
  }

  #[@test, @expect(IllegalArgumentException::class), @action(new RuntimeVersion('<7.0.0-dev'))]
  public function expectationMapRequiredOnCreate() {
    new ReplayState(null, null);
  }

  #[@test, @expect(IllegalArgumentException::class), @action(new RuntimeVersion('<7.0.0-dev'))]
  public function propertiesRequiredOnCreate() {
    new ReplayState(new HashTable(), null);
  }

  #[@test, @expect(Error::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function expectationMapRequiredOnCreate7() {
    new ReplayState(null, null);
  }

  #[@test, @expect(Error::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function propertiesRequiredOnCreate7() {
    new ReplayState(new HashTable(), null);
  }

  #[@test]
  public function canCreate() {
    new ReplayState(new HashTable(), new HashTable());
  }
  
  #[@test]
  public function canHandleInvocation() {
    $this->sut->handleInvocation(null, null);
  }

  #[@test]
  public function handleInvocation_withExistingExpectation_returnExpectationsReturnValue() {
    $myExpectation= new Expectation('foo');
    $myExpectation->setReturn('foobar');
    $expectationsList= new ExpectationList();
    $expectationsList->add($myExpectation);
    $this->expectationMap->put('foo', $expectationsList);
    $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', null));
  }

  #[@test]
  public function handleInvocation_missingExpectation_returnsNull() {
    $myExpectation= new Expectation('foo');
    $myExpectation->setReturn('foobar');
    
    $expectationsList= new ExpectationList();    
    $this->expectationMap->put('foo', $expectationsList);  
    $this->assertNull($this->sut->handleInvocation('foo', null));
  }

  #[@test]
  public function handleInvocation_ExpectationRepeatedTwice_returnExpectationsReturnValueTwice() {
    $myExpectation= new Expectation('foo');
    $myExpectation->setReturn('foobar');
    $myExpectation->setRepeat(2);
    $expectationsList= new ExpectationList();
    $expectationsList->add($myExpectation);
    $this->expectationMap->put('foo', $expectationsList);
    $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', null));
    $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', null));
    $this->assertNull($this->sut->handleInvocation('foo', null));
  }

  #[@test]
  public function handleInvocation_should_throw_exception_when_expectation_defines_one() {
    $expected= new \lang\XPException('foo');
    $myExpectation= new Expectation('foo');
    $myExpectation->setException($expected);
    $expectationsList= new ExpectationList();
    $expectationsList->add($myExpectation);
    $this->expectationMap->put('foo', $expectationsList);

    try {
      $this->sut->handleInvocation('foo', null);
      $this->fail('Exception not thrown.', null, $expect);
    } catch (\lang\XPException $e) {
      $this->assertEquals($expected, $e);
    }
  }
}