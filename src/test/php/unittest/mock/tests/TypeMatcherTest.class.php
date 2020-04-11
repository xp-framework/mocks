<?php namespace unittest\mock\tests;

use lang\Type;
use unittest\mock\MockRepository;
use unittest\mock\arguments\TypeMatcher;
use util\Date;

class TypeMatcherTest extends \unittest\TestCase {

  #[@test]
  public function canCreate() {
    new TypeMatcher('string');
  }
  
  #[@test]
  public function object_matches_object() {
    $this->assertTrue((new TypeMatcher('unittest.mock.tests.Value'))->matches(new Value()));
  }

  #[@test]
  public function object_does_not_match_string() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value'))->matches('a string'));
  }

  #[@test]
  public function object_does_not_match_subtype() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value'))->matches(new Date()));
  }

  #[@test]
  public function matches_should_not_match_parenttype() {
    $this->assertFalse((new TypeMatcher('unittest.mock.arguments.TypeMatcher'))->matches(new Value()));
  }
  
  #[@test]
  public function object_matches_null() {
    $this->assertTrue((new TypeMatcher('unittest.mock.tests.Value'))->matches(null));
  }
  
  #[@test]
  public function matches_should_not_match_null_if_defined_so() {
    $this->assertFalse((new TypeMatcher('unittest.mock.tests.Value', false))->matches(null));
  }
  
  #[@test]
  public function mock_repository_should_work_with() {
    $mockery= new MockRepository();
    $interface= $mockery->createMock('unittest.mock.tests.IComplexInterface');
    $interface->fooWithTypeHint(\unittest\mock\arguments\Arg::anyOfType('unittest.mock.tests.IEmptyInterface'));
  }
}