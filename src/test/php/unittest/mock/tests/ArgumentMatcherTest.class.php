<?php namespace unittest\mock\tests;

use unittest\Test;
use unittest\mock\arguments\Arg;
use util\Date;

/**
 * Testcase for the Arg convenience class
 *
 * @see   xp://unittest.mock.arguments.Arg
 */
class ArgumentMatcherTest extends \unittest\TestCase {

  /**
   * Callback for Arg::func()
   *
   * @param   string $arg
   * @return  bool
   */
  public static function matchEmpty($arg) {
    return '' === $arg;
  }

  #[Test]
  public function any_should_match_integers() {
    $this->assertTrue(Arg::any()->matches(1));
  }

  #[Test]
  public function any_should_match_strings() {
    $this->assertTrue(Arg::any()->matches(''));
  }

  #[Test]
  public function any_should_match_an_object() {
    $this->assertTrue(Arg::any()->matches(new Value()));
  }

  #[Test]
  public function any_should_match_null() {
    $this->assertTrue(Arg::any()->matches(null));
  }

  #[Test]
  public function dynamic_with_this_matchEmpty_should_match_empty_string() {
    $this->assertTrue(Arg::func('matchEmpty', $this)->matches(''));
  }

  #[Test]
  public function dynamic_with_static_matchEmpty_should_match_empty_string() {
    $this->assertTrue(Arg::func('matchEmpty', self::class)->matches(''));
  }

  #[Test]
  public function dynamic_with_matchEmpty_should_not_match_null() {
    $this->assertFalse(Arg::func('matchEmpty', $this)->matches(null));
  }

  #[Test]
  public function dynamic_with_matchEmpty_should_not_match_objects() {
    $this->assertFalse(Arg::func('matchEmpty', $this)->matches(new Value()));
  }

  #[Test]
  public function typeof_date_should_match_date_instance() {
    $this->assertTrue(Arg::anyOfType('util.Date')->matches(Date::now()));
  }

  #[Test]
  public function typeof_date_should_match_null() {
    $this->assertTrue(Arg::anyOfType('util.Date')->matches(null));
  }

  #[Test]
  public function typeof_date_should_not_match_objects() {
    $this->assertFalse(Arg::anyOfType('util.Date')->matches(new Value()));
  }

  #[Test]
  public function typeof_date_should_not_match_primitives() {
    $this->assertFalse(Arg::anyOfType('util.Date')->matches(1));
  }
}