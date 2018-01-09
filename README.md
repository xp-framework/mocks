Mocks
=====

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/mocks.svg)](http://travis-ci.org/xp-framework/mocks)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/mocks/version.png)](https://packagist.org/packages/xp-framework/mocks)

Mocks for the XP Framework.

Example
-------
Here's an example implementation:

```php
interface Context {
  public function hasPermission($name);
}

class UserService {
  private $context;

  public function __construct(Context $context) {
    $this->context= $context;
  }

  public function allUsers() {
    if (!$this->context->hasPermission('rt=all,rn=users')) {
      throw new IllegalAccessException('Permission denied!');
    }

    return []; // TODO: Actually do something:)
  }
}
```

This is how we can mock the `Context` interface:

```php
class UserServiceTest extends \unittest\TestCase {

  #[@test]
  public function allUsers_works_when_hasPermission_returns_true() {
    $mocks= new MockRepository();
    $context= $mocks->createMock('Context');
    $context->hasPermission('rt=all,rn=users')->returns(true);
    $mocks->replayAll();

    $fixture= new UserService($context);
    $this->assertEquals([], $fixture->allUsers());
  }
}
```

Further reading
---------------

See [XP RFC #0219](https://github.com/xp-framework/rfc/issues/219) for a detailled introduction.