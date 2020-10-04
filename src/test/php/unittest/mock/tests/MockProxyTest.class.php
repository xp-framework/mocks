<?php namespace unittest\mock\tests;

use lang\Type;
use lang\reflect\Proxy;
use unittest\Test;
use unittest\mock\{MockProxy, MockRepository};

/**
 * A proxy derivitive which implements additional mock behaviour definition
 * and validation.
 *
 * @see    xp://unittest.mock.MockProxy
 */
class MockProxyTest extends \unittest\TestCase {
  private $sut;

  /**
   * Creates the fixture;
   */
  public function setUp() {
    $this->sut= new MockProxy();
  }

  #[Test]
  public function canCreate() {
    new MockProxy(new MockRepository());
  }
  
  #[Test]
  public function canCallIsRecording() {
    $this->sut->isRecording();
  }

  #[Test]
  public function mockIsInRecordingStateInitially() {
    $this->assertTrue($this->sut->isRecording());
  }

  #[Test]
  public function canCallInvoke() {
    $this->sut->invoke(null, 'foo', null);
  }

  #[Test]
  public function invokeReturnsObject() {
    $this->assertInstanceOf('unittest.mock.MethodOptions', $this->sut->invoke(null, 'foo', null));
  }

  #[Test]
  public function canCallReplay() {
    $this->sut->replay();
  }

  #[Test]
  public function canCallIsReplaying() {
    $this->sut->isReplaying();
  }

  #[Test]
  public function notInReplayStateInitially() {
    $this->assertFalse($this->sut->isReplaying());
  }
  
  #[Test]
  public function stateChangesAfterReplayCall() {
    $this->assertTrue($this->sut->isRecording());
    $this->assertFalse($this->sut->isReplaying());
    $this->sut->replay();
    $this->assertFalse($this->sut->isRecording());
    $this->assertTrue($this->sut->isReplaying());
  }

  #[Test]
  public function callingReplayTwice_stateShouldNotChange() {
    $this->sut->invoke(null, 'foo', null)->returns('foo1')->repeat(1);
    $this->sut->invoke(null, 'foo', null)->returns('foo2')->repeat(1);
    $this->sut->invoke(null, 'bar', null)->returns('bar')->repeat(1);
    $this->sut->replay();

    $this->assertEquals('foo1', $this->sut->invoke(null, 'foo', null));
    $this->assertEquals('bar', $this->sut->invoke(null, 'bar', null));

    $this->sut->replay(); //should not start over
    $this->assertEquals('foo2', $this->sut->invoke(null, 'foo', null));
    $this->assertEquals(null, $this->sut->invoke(null, 'bar', null));
  }
}