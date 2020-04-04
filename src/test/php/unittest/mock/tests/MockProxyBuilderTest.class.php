<?php namespace unittest\mock\tests;

use lang\ClassLoader;
use lang\Error;
use lang\IllegalArgumentException;
use lang\XPClass;
use lang\reflect\InvocationHandler;
use unittest\TestCase;
use unittest\actions\RuntimeVersion;
use unittest\actions\VerifyThat;
use unittest\mock\MockProxyBuilder;
use util\Objects;
use util\XPIterator;

class MockProxyBuilderTest extends TestCase {
  public
    $handler       = null,
    $iteratorClass = null,
    $observerClass = null;

  /** @return void */
  public function setUp() {
    $this->handler= newinstance(InvocationHandler::class, [], '{
      public $invocations= [];

      public function invoke($proxy, $method, $args) { 
        $this->invocations[$method."_".sizeof($args)]= $args;
      }
    }');
    $this->iteratorClass= XPClass::forName('util.XPIterator');
    $this->observerClass= XPClass::forName('util.Observer');
  }

  /**
   * Helper method which returns a proxy instance for a given list of
   * interfaces, using the default classloader and the handler defined
   * in setUp()
   *
   * @param   lang.XPClass[] interfaces
   * @return  lang.reflect.Proxy
   */
  protected function proxyInstanceFor($interfaces) {
    return (new MockProxyBuilder())->createProxyInstance(
      ClassLoader::getDefault(),
      $interfaces, 
      $this->handler
    );
  }
  
  /**
   * Helper method which returns a proxy class for a given list of
   * interfaces, using the default classloader and the handler defined
   * in setUp()
   *
   * @param   lang.XPClass[] interfaces
   * @return  lang.XPClass
   */
  protected function proxyClassFor($interfaces) {
    return (new MockProxyBuilder())->createProxyClass(
      ClassLoader::getDefault(),
      $interfaces
    );
  }

  /**
   * Assertion helper
   *
   * @param  var[] $array
   * @param  var $value
   * @throws unittest.AssertionFailedError
   */
  protected function assertContains($array, $value) {
    foreach ($array as $element) {
      if (Objects::equal($element, $value)) return;
    }
    $this->fail('Value not contained', $value, $array);
  }

  #[@test, @expect(IllegalArgumentException::class), @action(new RuntimeVersion('<7.0.0-dev'))]
  public function nullClassLoader() {
    (new MockProxyBuilder())->createProxyClass(null, [$this->iteratorClass]);
  }

  #[@test, @expect(IllegalArgumentException::class), @action(new RuntimeVersion('<7.0.0-dev'))]
  public function nullInterfaces() {
    (new MockProxyBuilder())->createProxyClass(ClassLoader::getDefault(), null);
  }

  #[@test, @expect(Error::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function nullClassLoader7() {
    (new MockProxyBuilder())->createProxyClass(null, [$this->iteratorClass]);
  }

  #[@test, @expect(Error::class), @action(new RuntimeVersion('>=7.0.0-dev'))]
  public function nullInterfaces7() {
    (new MockProxyBuilder())->createProxyClass(ClassLoader::getDefault(), null);
  }

  #[@test]
  public function proxyClassNamesGetPrefixed() {
    $class= $this->proxyClassFor([$this->iteratorClass]);
    $this->assertEquals(MockProxyBuilder::PREFIX, substr($class->getName(), 0, strlen(MockProxyBuilder::PREFIX)));
  }

  #[@test]
  public function classesEqualForSameInterfaceList() {
    $c1= $this->proxyClassFor([$this->iteratorClass]);
    $c2= $this->proxyClassFor([$this->iteratorClass]);
    $c3= $this->proxyClassFor([$this->iteratorClass, $this->observerClass]);

    $this->assertEquals($c1, $c2);
    $this->assertNotEquals($c1, $c3);
  }

  #[@test]
  public function iteratorInterfaceIsImplemented() {
    $class= $this->proxyClassFor([$this->iteratorClass]);
    $interfaces= $class->getInterfaces();
    $this->assertEquals(2 + class_exists(\lang\Generic::class), sizeof($interfaces));
    $this->assertContains($interfaces, $this->iteratorClass);
  }

  #[@test]
  public function allInterfacesAreImplemented() {
    $class= $this->proxyClassFor([$this->iteratorClass, $this->observerClass]);
    $interfaces= $class->getInterfaces();
    $this->assertEquals(3 + class_exists(\lang\Generic::class), sizeof($interfaces));
    $this->assertContains($interfaces, $this->iteratorClass);
    $this->assertContains($interfaces, $this->observerClass);
  }

  #[@test]
  public function iteratorMethods() {
    $class= $this->proxyClassFor([$this->iteratorClass]);
    $this->assertEquals(
      [true, true],
      [$class->hasMethod('hasNext'), $class->hasMethod('next')]
    );
  }

  #[@test]
  public function iteratorNextInvoked() {
    $proxy= $this->proxyInstanceFor([$this->iteratorClass]);
    $proxy->next();
    $this->assertEquals([], $this->handler->invocations['next_0']);
  }
  
  #[@test, @expect(IllegalArgumentException::class)]
  public function cannotCreateProxiesForClasses() {
    $this->proxyInstanceFor([new XPClass(Value::class)]);
  }
  
  #[@test]
  public function allowDoubledInterfaceMethod() {
    $newIteratorClass= ClassLoader::defineInterface('util.NewIterator', 'util.XPIterator');
    $this->proxyInstanceFor([XPClass::forName('util.XPIterator'), $newIteratorClass]);
  }
  
  #[@test]
  public function overloadedMethod() {
    $proxy= $this->proxyInstanceFor([XPClass::forName('unittest.mock.tests.OverloadedInterface')]);
    $proxy->overloaded('foo');
    $proxy->overloaded('foo', 'bar');
    $this->assertEquals(['foo'], $this->handler->invocations['overloaded_1']);
    $this->assertEquals(['foo', 'bar'], $this->handler->invocations['overloaded_2']);
  }

  #[@test]
  public function static_initializer_gets_overwritten() {
    $staticInited= newinstance(Value::class, [], '{
      private static $counter= 0;
      static function __static() {
        self::$counter++;
      }

      public function counter() {
        return self::$counter;
      }
    }');

    $proxyClass= (new MockProxyBuilder())->createProxyClass(ClassLoader::getDefault(), [], typeof($staticInited));
    $this->assertEquals(1, $proxyClass->newInstance(null)->counter());
  }

  #[@test]
  public function proxyClass_implements_IMockProxy() {
    $proxy= $this->proxyClassFor([$this->iteratorClass]);
    $this->assertContains($proxy->getInterfaces(), XPClass::forName('unittest.mock.IMockProxy'));
  }

  #[@test]
  public function concrete_methods_should_not_be_changed_by_default() {
    $proxyBuilder= new MockProxyBuilder();
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      [],
      XPClass::forName('unittest.mock.tests.AbstractDummy')
    );
    $proxy= $class->newInstance($this->handler);
    $this->assertEquals('concreteMethod', $proxy->concreteMethod());
  }

  #[@test]
  public function abstract_methods_should_delegated_to_handler() {
    $proxyBuilder= new MockProxyBuilder();
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      [],
      XPClass::forName('unittest.mock.tests.AbstractDummy')
    );
    $proxy= $class->newInstance($this->handler);
    $proxy->abstractMethod();
    $this->assertInstanceOf('var[]', $this->handler->invocations['abstractMethod_0']);
  }

  #[@test]
  public function with_overwriteAll_abstract_methods_should_delegated_to_handler() {
    $proxyBuilder= new MockProxyBuilder();
    $proxyBuilder->setOverwriteExisting(true);
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      [],
      XPClass::forName('unittest.mock.tests.AbstractDummy')
    );
    $proxy= $class->newInstance($this->handler);
    $proxy->concreteMethod();
    $this->assertInstanceOf('var[]', $this->handler->invocations['concreteMethod_0']);
  }

  #[@test, @action(new VerifyThat(function() { return class_exists(\lang\Generic::class); }))]
  public function reserved_methods_should_not_be_overridden() {
    $proxyBuilder= new MockProxyBuilder();
    $proxyBuilder->setOverwriteExisting(true);
    $class= $proxyBuilder->createProxyClass(ClassLoader::getDefault(),
      [],
      XPClass::forName('unittest.mock.tests.AbstractDummy')
    );
    $proxy= $class->newInstance($this->handler);
    $proxy->equals(new Value());
    $this->assertFalse(isset($this->handler->invocations['equals_1']));
  }

  #[@test]
  public function namespaced_parameters_handled_correctly() {
    $class= $this->proxyClassFor([ClassLoader::defineInterface('net.xp_framework.unittest.test.mock.NSInterface', [], '{
      public function fixture(\util\Date $param);
    }')]);
    $this->assertEquals(
      XPClass::forName('util.Date'),
      $class->getMethod('fixture')->getParameters()[0]->getType()
    );
  }
}
