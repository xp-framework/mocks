<?php namespace unittest\mock\tests;
 
use lang\reflect\Proxy;
use lang\{ClassNotFoundException, IllegalArgumentException, IllegalStateException};
use unittest\mock\arguments\Arg;
use unittest\mock\tests\{IComplexInterface, IEmptyInterface, PartiallyImplementedAbstractDummy};
use unittest\mock\{ExpectationViolationException, MockRepository};
use unittest\{Expect, Test, TestCase};


/**
 * Class for automaticly stubbing interfaces
 *
 * @see   xp://unittest.mock.MockRepository
 */
class MockRepositoryTest extends TestCase {
  private $fixture= null;
  
  /**
   * Creates the fixture;
   *
   */
  public function setUp() {
    $this->fixture= new MockRepository();
  }
    
  /**
   * Can create.
   *
   */
  #[Test]
  public function canCreate() {
    new MockRepository();
  }

  /**
   * Can create mock for empty interface
   *
   */
  #[Test]
  public function canCreateMockForEmptyInterface() {
    $object= $this->fixture->createMock('unittest.mock.tests.IEmptyInterface');
    $this->assertInstanceOf(IEmptyInterface::class, $object);
  }

  /**
   * Can create mock for non-empty interface
   *
   */
  #[Test]
  public function canCreateMockForComplexInterface() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $this->assertInstanceOf(IComplexInterface::class, $object);
  }

  /**
   * Can create mock for non-empty interface
   *
   */
  #[Test, Expect(ClassNotFoundException::class)]
  public function cannotCreateMockForUnknownTypes() {
    $this->fixture->createMock('foooooo.Unknown');
  }

  /**
   * Can create mock for non-empty interface
   *
   */
  #[Test, Expect(IllegalArgumentException::class)]
  public function cannotCreateMockForNonXPClassTypes() {
    $this->fixture->createMock('string');
  }

  /**
   * Can call replay
   *
   */
  #[Test]
  public function canCallReplay() {
    $mock= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $mock->_replayMock();
  }

  /**
   * Can call interface methods
   *
   */
  #[Test]
  public function canCallInterfaceMethods() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->foo();
  }

  /**
   * Can call returns() on mocked object
   *
   */
  #[Test]
  public function canCallReturnsFluently() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->foo()->returns(null);
  }

  /**
   * Defined value returned in replay mode
   *
   */
  #[Test]
  public function canDefineReturnValue() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $return = new Value();
    $object->foo()->returns($return);

    $object->_replayMock();
    $this->assertTrue($object->foo()=== $return);
  }

  /**
   * If no expectations are left, NULL is returned
   */
  #[Test]
  public function missingExpectationLeadsToNull() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $object->_replayMock();
    $this->assertNull($object->foo());
    $this->assertNull($object->foo());
    $this->assertNull($object->foo());
  }
  
  /**
   * If no expectations are left, NULL is returned
   *
   */
  #[Test]
  public function recordedReturnsAreInCorrectOrder() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $return1='foo';
    $return2='bar';
    $return3='baz';
    
    $object->foo()->returns($return1)->repeat(1);
    $object->foo()->returns($return2)->repeat(2);
    $object->foo()->returns($return3)->repeat(1);
    $object->_replayMock();
    
    $this->assertEquals($return1, $object->foo());
    $this->assertEquals($return2, $object->foo());
    $this->assertEquals($return2, $object->foo());
    $this->assertEquals($return3, $object->foo());
    $this->assertNull($object->foo());

  }
  
  /**
   * Mockery has replayAll method                       
   *
   */
  #[Test]
  public function canCallReplayAll() {
    $this->fixture->replayAll();
  }
  
  /**
   * All mocks created by one mockery are set to replay mode when
   * replayAll is called.
   *
   */
  #[Test]
  public function replayAllSetsAllMocksInReplayMode() {
    $object1=$this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object2=$this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object3=$this->fixture->createMock('unittest.mock.tests.IEmptyInterface');
   
    $this->assertTrue($object1->_isMockRecording());
    $this->assertTrue($object2->_isMockRecording());
    $this->assertTrue($object3->_isMockRecording());

    $this->fixture->replayAll();
    
    $this->assertTrue($object1->_isMockReplaying());
    $this->assertTrue($object2->_isMockReplaying());
    $this->assertTrue($object3->_isMockReplaying());
  }
  
  /**
   * 'Partial' mocks are also possible.
   *
   */
  #[Test]
  public function can_createMock_fromAbstractClass() {
    $obj= $this->fixture->createMock('unittest.mock.tests.PartiallyImplementedAbstractDummy', true);
    $this->assertInstanceOf(PartiallyImplementedAbstractDummy::class, $obj);
  }
  
  /**
   * Abstract methods should be implemented (and delegated to the handler).
   *
   */
  #[Test]
  public function abstractMethodAreMocked() {
    $obj= $this->fixture->createMock('unittest.mock.tests.PartiallyImplementedAbstractDummy');

    $baz_expect='BAAAAZ!';
    $obj->baz(null)->returns($baz_expect);

    $bar_expect='BAAARRR';
    $obj->bar(null, null)->returns($bar_expect);

    $obj->_replayMock();

    $this->assertEquals($baz_expect, $obj->baz(null));
    $this->assertEquals($bar_expect, $obj->bar(null, null));
  }

  /**
   * The concretely implemented methods should not be mocked if
   * overrideAll is unset.
   *
   */
  #[Test]
  public function concreteMethodsNotMocked() {
    $obj= $this->fixture->createMock('unittest.mock.tests.PartiallyImplementedAbstractDummy', false);

    $this->assertEquals('IComplexInterface.foo', $obj->foo());
  }

  /**
   * Tests mocking concrete classes
   *
   */
  #[Test]
  public function concreteClassesMocked_whenSpecifiedSo() {
    $obj= $this->fixture->createMock(
      'unittest.mock.tests.PartiallyImplementedAbstractDummy',
      true
    );

    $foo_expect= 'fooooo';
    $obj->foo()->returns($foo_expect);
    $obj->_replayMock();

    $this->assertEquals($foo_expect, $obj->foo());
  }

  /**
   * Can define return object for two calls with repeat
   *
   */
  #[Test]
  public function canCallRepeat() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $expected= 'myFooReturn';
    $object->foo()->returns($expected)->repeat(2);
    $this->fixture->replayAll();

    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
    $this->assertNull($object->foo());
  }


  /**
   * Can define return object for two calls with repeat
   *
   */
  #[Test]
  public function canCallRepeatAny() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $expected= 'myFooReturn';
    $object->foo()->returns($expected)->repeatAny();
    $this->fixture->replayAll();

    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
    $this->assertEquals($expected, $object->foo());
  }

  /**
   * When recording calls, the arguments should be considered.
   *
   */
  #[Test]
  public function method_arguments_should_be_considered_in_recodring() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $conf1Expect='c1';
    $conf2Expect='c2';
    $object->bar('X', 'Conf1')->returns($conf1Expect);
    $object->bar('X', 'Conf2')->returns($conf2Expect);
    $this->fixture->replayAll();

    $this->assertEquals($conf2Expect, $object->bar('X', 'Conf2'));
    $this->assertEquals($conf1Expect, $object->bar('X', 'Conf1'));
  }

  /**
   * It should be possible to define expectations for any arguments.
   */
  #[Test]
  public function arg_any_accepts_all_arguments() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $expect='c1';
    $object->bar(Arg::any(), Arg::any())->returns($expect)->repeatAny();
    $this->fixture->replayAll();

    $this->assertEquals($expect, $object->bar(null, null));
    $this->assertEquals($expect, $object->bar(34, 'foo'));
    $this->assertEquals($expect, $object->bar(23.0, new Value()));
  }

  /**
   * Unexpected calls should fail, when _verifyMock is called.
   */
  #[Test, Expect(ExpectationViolationException::class)]
  public function unexpected_calls_should_fail_on_mock_object_verification() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $object->foo(); //expect a call to foo
    $this->fixture->replayAll();

    $object->_verifyMock(); // expected call to foo missing -> fail
  }

   /**
   * verifyAll() verifies all mocks of that mockery.
   */
  #[Test]
  public function verfyAll_should_verfiy_all_mocks__positive_case() {
    $object1= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object2= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object3= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object4= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $object1->foo();
    $object2->foo();
    $object3->foo();
    $object4->foo();
    $this->fixture->replayAll();

    $object1->foo();
    $object2->foo();
    $object3->foo();
    $object4->foo();
    $this->fixture->verifyAll();
  }

  /**
   * verifyAll() verifies all mocks of that mockery.
   */
  #[Test, Expect(ExpectationViolationException::class)]
  public function verfyAll_should_verfiy_all_mocks__negative_case() {
    $object1= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object2= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object3= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object4= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    
    $object1->foo();
    $object2->foo();
    $object3->foo();
    $object4->foo();
    $this->fixture->replayAll();

    $object1->foo();
    $object2->foo();
    $object3->foo();
    //missed call on $object4->foo();
    $this->fixture->verifyAll();
  }

  /**
   * It should be possible to define an exception that is thrown on a call
   */
  #[Test]
  public function canCall_throws_on_mock_object() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->foo()->throws(new \lang\XPException('foo'));
  }

  /**
   * When a expectation with 'throws' is defined on a mock, then this exception
   * should be thrown when the expected method is called in replay mode.
   */
  #[Test]
  public function mock_with_throws_should_throw_the_specified_exception() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $expected= new \lang\IllegalStateException('foo');
    $object->foo()->throws($expected);

    $this->fixture->replayAll();
    try { $object->foo(); }
    catch(\lang\IllegalStateException $actual) {
      $this->assertEquals($expected, $actual);
      return;
    }

    $this->fail('No exception thrown.', null, $expected);
  }
  
  /**
   * Test
   *
   */
  #[Test]
  public function property_behavior_get_methods_should_return_null_by_default() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    
    $object->getFoo()->propertyBehavior();
    $this->fixture->replayAll();
    
    $this->assertNull($object->getFoo());
  }

  /**
   * Test
   *
   */
  #[Test]
  public function property_behavior_get_methods_should_return_value_set_by_setter() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->getFoo()->propertyBehavior();
    $this->fixture->replayAll();

    $object->setFoo(7);
    $this->assertEquals(7, $object->getFoo());
    
    $object->setFoo('blub');
    $this->assertEquals('blub', $object->getFoo());
  }

  /**
   * Test
   *
   */
  #[Test, Expect(IllegalStateException::class)]
  public function property_behavior_should_throw_exception_if_returns_is_set_before() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $object->getFoo()->returns('x');
    $object->getFoo()->propertyBehavior();

    
    $this->fixture->replayAll();
  }
  
  /**
   * Test
   *
   */
  #[Test, Expect(IllegalStateException::class)]
  public function property_behavior_should_throw_exception_if_returns_is_set_afterwards() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');

    $object->getFoo()->propertyBehavior();
    $object->getFoo()->returns('x');

    
    $this->fixture->replayAll();
  }
  
  /**
   * Test
   *
   */
  #[Test]
  public function property_behavior_return_value_should_be_predefinable() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->getFoo()->propertyBehavior()->returns(10);
    $this->fixture->replayAll();

    $this->assertEquals(10, $object->getFoo());

    $object->setFoo(7);
    $this->assertEquals(7, $object->getFoo());
  }
  
  /**
   * Test
   *
   */
  #[Test, Expect(IllegalStateException::class)]
  public function property_behavior_should_only_be_applicable_to_getters_and_setters() {
    $object= $this->fixture->createMock('unittest.mock.tests.IComplexInterface');
    $object->foo()->propertyBehavior();
  }
}