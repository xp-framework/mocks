<?php namespace unittest\mock\tests;
 
use lang\{Error, IllegalArgumentException};
use unittest\mock\{Expectation, ExpectationList, ReplayState};
use unittest\{Expect, Test, TestCase};
use util\collections\HashTable;

/**
 * Testcase for ReplayState
 *
 * @see   xp://unittest.mock.ReplayState
 */
class ReplayStateTest extends TestCase {
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

  #[Test, Expect(Error::class)]
  public function expectationMapRequiredOnCreate() {
    new ReplayState(null, null);
  }

  #[Test, Expect(Error::class)]
  public function propertiesRequiredOnCreate() {
    new ReplayState(new HashTable(), null);
  }

  #[Test]
  public function canCreate() {
    new ReplayState(new HashTable(), new HashTable());
  }
  
  #[Test]
  public function canHandleInvocation() {
    $this->sut->handleInvocation('', []);
  }

  #[Test]
  public function handleInvocation_withExistingExpectation_returnExpectationsReturnValue() {
    $myExpectation= new Expectation('foo');
    $myExpectation->setReturn('foobar');
    $expectationsList= new ExpectationList();
    $expectationsList->add($myExpectation);
    $this->expectationMap->put('foo', $expectationsList);
    $this->assertEquals($myExpectation->getReturn(), $this->sut->handleInvocation('foo', null));
  }

  #[Test]
  public function handleInvocation_missingExpectation_returnsNull() {
    $myExpectation= new Expectation('foo');
    $myExpectation->setReturn('foobar');
    
    $expectationsList= new ExpectationList();    
    $this->expectationMap->put('foo', $expectationsList);  
    $this->assertNull($this->sut->handleInvocation('foo', null));
  }

  #[Test]
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

  #[Test]
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