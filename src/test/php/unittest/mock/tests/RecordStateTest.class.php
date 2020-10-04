<?php namespace unittest\mock\tests;
 
use lang\{Error, IllegalArgumentException};
use unittest\mock\{Expectation, ExpectationList, RecordState};
use unittest\{Expect, Test};
use util\collections\HashTable;

class RecordStateTest extends \unittest\TestCase {
  private 
    $sut            = null,
    $expectationMap = null;
  
  /**
   * Creates the fixture
   */
  public function setUp() {
    $this->expectationMap= new HashTable();
    $this->sut= new RecordState($this->expectationMap);
  }
    
  #[Test, Expect(Error::class)]
  public function expectationMapRequiredOnCreate7() {
    new RecordState(null);
  }

  #[Test]
  public function canCreate() {
    new RecordState(new HashTable());
  }

  #[Test]
  public function canHandleInvocation() {
    $this->sut->handleInvocation('methodName', null);
  }

  #[Test]
  public function newExpectationCreatedOnHandleInvocation() {
    $this->sut->handleInvocation('foo', null);
    $this->assertEquals(1, $this->expectationMap->size());
    $expectationList= $this->expectationMap->get('foo');
    $this->assertInstanceOf(ExpectationList::class, $expectationList);
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
  }

  #[Test]
  public function newExpectationCreatedOnHandleInvocation_twoDifferentMethods() {
    $this->sut->handleInvocation('foo', null);
    $this->sut->handleInvocation('bar', null);
    $this->assertInstanceOf(Expectation::class, $this->expectationMap->get('foo')->getNext([]));
    $this->assertInstanceOf(Expectation::class, $this->expectationMap->get('bar')->getNext([]));
  }

  #[Test]
  public function newExpectationCreatedOn_EACH_HandleInvocationCall() {
    $this->sut->handleInvocation('foo', null);
    $this->sut->handleInvocation('foo', null);
    $expectationList= $this->expectationMap->get('foo');

    $this->assertInstanceOf(ExpectationList::class, $expectationList);
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
  }

  #[Test]
  public function method_call_should_set_arguments() {
    $args= ['1', 2, 3.0];
    $this->sut->handleInvocation('foo', $args);

    $expectationList= $this->expectationMap->get('foo');
    $expectedExpectaton= $expectationList->getNext($args);
    $this->assertInstanceOf('unittest.mock.Expectation', $expectedExpectaton);
  }
}