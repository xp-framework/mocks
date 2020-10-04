<?php namespace unittest\mock\tests;

use lang\{Error, IllegalArgumentException};
use unittest\mock\{Expectation, ExpectationList};
use unittest\{Expect, Test, TestCase};

/**
 * Test cases for the ExpectationList class
 *
 * @see   xp://unittest.mock.ExpectationList
 */
class ExpectationListTest extends TestCase {
  private $sut= null;

  /**
   * Creates the fixture;
   */
  public function setUp() {
    $this->sut= new ExpectationList();
  }
    
  #[Test]
  public function canCreate() {
    new ExpectationList();
  }

  #[Test]
  public function canCallGetNext() {
    $this->sut->getNext([]);
  }
  
  #[Test]
  public function getNext_returnNullByDefault() {
    $this->assertNull($this->sut->getNext([]));
  }
  
  #[Test]
  public function canAddExpectation() {
    $this->sut->add(new Expectation('method'));
  }
  
  #[Test]
  public function getNextReturnsAddedExpectation() {
    $expect= new Expectation('method');
    $this->sut->add($expect);    
    $this->assertEquals($expect, $this->sut->getNext([]));
  }
  
  #[Test]
  public function getNextReturns_should_return_last_expectation_over_and_over() {
    $expect= new Expectation('method');
    $this->sut->add($expect);
    $this->assertEquals($expect, $this->sut->getNext([]));
    $this->assertEquals($expect, $this->sut->getNext([]));
    $this->assertEquals($expect, $this->sut->getNext([]));
  }
  
  #[Test]
  public function getNext_SameExpectationTwice_whenRepeatIs2() {
    $expect= new Expectation('method');
    $expect->setRepeat(2);
    $this->sut->add($expect);
    $this->assertEquals($expect, $this->sut->getNext([]));
    $this->assertEquals($expect, $this->sut->getNext([]));
    $this->assertNull($this->sut->getNext([]));
  }

  #[Test]
  public function should_provide_access_to_left_expectations() {
    $expect= new Expectation('method');
    $this->sut->add($expect);
    $list= $this->sut->getExpectations();
    $this->assertEquals(1, $list->size());
    $this->assertEquals($expect, $list[0]);
  }

  #[Test]
  public function should_provide_access_to_used_expectations() {
    $expect= new Expectation('method');
    $this->sut->add($expect);
    $this->sut->getNext([]);    
    $list= $this->sut->getCalled();
    $this->assertEquals(1, $list->size());
    $this->assertEquals($expect, $list[0]);
  }

  #[Test]
  public function expectation_should_be_moved_to_calledList_after_usage() {
    $expect= new Expectation('method');
    $this->sut->add($expect);
    $list= $this->sut->getExpectations();
    $this->assertEquals(1, $list->size());
    $this->assertEquals($expect, $list[0]);
    $this->sut->getNext([]);
    $list= $this->sut->getCalled();
    $this->assertEquals(1, $list->size());
    $this->assertEquals($expect, $list[0]);
  }
}