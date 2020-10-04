<?php namespace unittest\mock\tests;

use lang\Type;
use unittest\Test;
use unittest\mock\MockRepository;
use unittest\mock\arguments\TypeMatcher;
use util\Date;

class TypeMatcherTest extends \unittest\TestCase {

  #[Test]
  public function canCreate() {
    new TypeMatcher('string');
  }
  
  #[Test]
  public function object_matches_object() {
    $this->assertTrue((new TypeMatcher('unittest.mock.tests.Value'))->matches(new Value()));
  }

  #[Test]
  public function object_does_not_match_string() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value'))->matches('a string'));
  }

  #[Test]
  public function object_does_not_match_subtype() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value'))->matches(new Date()));
  }

  #[Test]
  public function matches_should_not_match_parenttype() {
    $this->assertFalse((new TypeMatcher('unittest.mock.arguments.TypeMatcher'))->matches(new Value()));
  }
  
  #[Test]
  public function object_matches_null() {
    $this->assertTrue((new TypeMatcher('unittest.mock.tests.Value'))->matches(null));
  }
  
  #[Test]
  public function matches_should_not_match_null_if_defined_so() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value', false))->matches(null));
  }
  
  #[Test]
  public function mock_repository_should_work_with() {
    $mockery= new MockRepository();
    $interface= $mockery->createMock('unittest.mock.tests.IComplexInterface');
    $interface->fooWithTypeHint(\unittest\mock\arguments\Arg::anyOfType('unittest.mock.tests.IEmptyInterface'));
  }
}