<?php namespace unittest\mock\tests;
 
use lang\IllegalArgumentException;
use lang\Error;
use unittest\mock\ExpectationList;
use unittest\mock\Expectation;
use unittest\mock\RecordState;
use util\collections\HashTable;
use unittest\actions\RuntimeVersion;

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
    
  #[@test, @expect(IllegalArgumentException::class), @action(new RuntimeVersion('<7.0.0-dev'))]
  public function expectationMapRequiredOnCreate() {
    new RecordState(null);
  }

  #[@test, @expect(Error::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function expectationMapRequiredOnCreate7() {
    new RecordState(null);
  }

  #[@test]
  public function canCreate() {
    new RecordState(new HashTable());
  }

  #[@test]
  public function canHandleInvocation() {
    $this->sut->handleInvocation('methodName', null);
  }

  #[@test]
  public function newExpectationCreatedOnHandleInvocation() {
    $this->sut->handleInvocation('foo', null);
    $this->assertEquals(1, $this->expectationMap->size());
    $expectationList= $this->expectationMap->get('foo');
    $this->assertInstanceOf(ExpectationList::class, $expectationList);
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
  }

  #[@test]
  public function newExpectationCreatedOnHandleInvocation_twoDifferentMethods() {
    $this->sut->handleInvocation('foo', null);
    $this->sut->handleInvocation('bar', null);
    $this->assertInstanceOf(Expectation::class, $this->expectationMap->get('foo')->getNext([]));
    $this->assertInstanceOf(Expectation::class, $this->expectationMap->get('bar')->getNext([]));
  }

  #[@test]
  public function newExpectationCreatedOn_EACH_HandleInvocationCall() {
    $this->sut->handleInvocation('foo', null);
    $this->sut->handleInvocation('foo', null);
    $expectationList= $this->expectationMap->get('foo');

    $this->assertInstanceOf(ExpectationList::class, $expectationList);
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
    $this->assertInstanceOf(Expectation::class, $expectationList->getNext([]));
  }

  #[@test]
  public function method_call_should_set_arguments() {
    $args= ['1', 2, 3.0];
    $this->sut->handleInvocation('foo', $args);

    $expectationList= $this->expectationMap->get('foo');
    $expectedExpectaton= $expectationList->getNext($args);
    $this->assertInstanceOf('unittest.mock.Expectation', $expectedExpectaton);
  }
}
