<?php namespace unittest\mock\tests;

use unittest\mock\arguments\PatternMatcher;
use unittest\{Test, Values};

/**
 * Testcase for PatternMatcher class
 *
 * @see   xp://unittest.mock.arguments.PatternMatcher
 */
class PatternMatcherTest extends \unittest\TestCase {

  #[Test]
  public function construction_should_work_with_string_parameter() {
    new PatternMatcher('foobar');
  }

  #[Test, Values(['foooo', 'foo', 'foo ', 'foo asdfa'])]
  public function prefix_match_test_matches($value) {
    $this->assertTrue((new PatternMatcher('/^foo/'))->matches($value));
  }

  #[Test, Values(['xfoo', ' foo '])]
  public function prefix_match_test_does_not_match($value) {
    $this->assertFalse((new PatternMatcher('/^foo/'))->matches($value));
  }

  #[Test]
  public function exact_match_test() {
    $this->assertTrue((new PatternMatcher('/^foo$/'))->matches('foo'));
  }

  #[Test, Values(['foooo', 'foo ', 'foo asdfa', 'xfoox', ' foo '])]
  public function exact_match_negative_tests($value) {
    $this->assertFalse((new PatternMatcher('/^foo$/'))->matches($value));
  }

  #[Test, Values(['foooo', 'fooooooooo', 'adsfafdsfooooooooo', 'asdfaf fooo dsfasfd'])]
  public function pattern_match_test($value) {
    $this->assertTrue((new PatternMatcher('/fo+o.*/'))->matches($value));
  }

  #[Test, Values(['fobo', 'fo'])]
  public function pattern_match_test_negative_tests($value) {
    $this->assertFalse((new PatternMatcher('/fo+o.*/'))->matches($value));
  }
}