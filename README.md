# PHP Unit Mock Extension

An PHP extension to allow multiple calls to a method with different counts and different arguments.

## Installation

```bash
composer require jcid/phpunit-mock-extension
```

## The problem

Currently if you want to do multiple method calls to a PHP Unit mock object with different argument and return statements you can to use the `$this->at($index)` method. The problem with this method is that you cannot check if the method is getting called to much for instance a third time like in the example below. This is the case because PHP Unit does not use the arguments to select the matcher.

```php
class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testExample()
    {
        $mock = $this->getMock(\stdClass::class, ['mymethod']);
        $mock->expects($this->at(0))
            ->method('mymethod')
            ->with('aaa')
            ->willReturn('bbb');
        $mock->expects($this->at(1))
            ->method('mymethod')
            ->with('bbb')
            ->willReturn('ccc');

        $this->assertSame('bbb', $mock->mymethod('aaa'));
        $this->assertSame('ccc', $mock->mymethod('bbb'));
        $mock->mymethod('different');
    }
}
```

An other solution is to use the logic or method's to define the arguments. But I personally don't like this method because it decouples the arguments from the return values and you have no control over the sequence or combination of arguments and returns.

```php
class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testLogicOr()
    {
        $mock = $this->getMock(\stdClass::class, ['mymethod']);
        $mock->expects($this->exactly(2))
            ->method('mymethod')
            ->with($this->logicalOr(
                $this->equalTo('aaa'),
                $this->equalTo('bbb')
            ))
            ->will($this->onConsecutiveCalls('bbb', 'ccc'));

        $this->assertSame('bbb', $mock->mymethod('aaa'));
        $this->assertSame('ccc', $mock->mymethod('bbb'));
    }
}
```

But there is a third solution provided by PHP Unit the `$this->returnValueMap(*array*)` method. This method is a bit strange because the last argument of the array is the return value. But it couples the arguments and the return values and that's nice. You can now also check the amount of times the method is called with the right combination.

```php
class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testReturnValueMap()
    {
        $mock = $this->getMock(\stdClass::class, ['mymethod']);
        $mock->expects($this->exactly(2))
            ->method('mymethod')
            ->will($this->returnValueMap([
                ['aaa', 'bbb'],
                ['bbb', 'ccc'],
            ]));

        $this->assertSame('bbb', $mock->mymethod('aaa'));
        $this->assertSame('ccc', $mock->mymethod('bbb'));
    }
}
```

The second problem with the `$this->returnValueMap(*array*)` method is that the arguments use a strict comparison check and if you use a value holder like we do this will fail.

```php
class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testReturnValueMapStrict()
    {
        $mock = $this->getMock(\stdClass::class, ['mymethod']);
        $mock->expects($this->exactly(2))
            ->method('mymethod')
            ->will($this->returnValueMap([
                [new DummyValueHolder('aaa'), 'bbb'],
                ['bbb', 'ccc'],
            ]));

        $this->assertSame('bbb', $mock->mymethod(new DummyValueHolder('aaa')));
        $this->assertSame('ccc', $mock->mymethod('bbb'));
    }
}

class DummyValueHolder
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
```

## Our solution

We created this library which uses a custom MockObject Matcher and custom MockObject to decide how and how much times we should return a specific value. The idea is based on the idea of `$this->at($index)` but has al the features we want.

We use a custom builder to create the sequence of calls to one particular method and then define the `expects` with our custom matcher created by the builder and the use the return stub provided by it.

```php
use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Builder;

class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testSolution()
    {
        $builder = new Builder();
        $builder->expects($this->exactly(2))
            ->with(new DummyValueHolder('aaa'))
            ->willReturnSelf();
        $builder->expects($this->once())
            ->with('second method call matcher')
            ->willReturn('second return');
        $builder->expects($this->exactly(2))
            ->with('third method call matcher')
            ->willReturn('third return');

        $dummy = $this->getMock(\stdClass::class, ['test']);
        $dummy->expects($builder->matcher())->method('test')->will($builder->mapper());

        $this->assertSame($dummy, $dummy->test(new DummyValueHolder('aaa')));
        $this->assertSame($dummy, $dummy->test(new DummyValueHolder('aaa')));
        $this->assertSame('second return', $dummy->test('second method call matcher'));
        $this->assertSame('third return', $dummy->test('third method call matcher'));
        $this->assertSame('third return', $dummy->test('third method call matcher'));
    }
}

class DummyValueHolder
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
```

## Inspiration

This library is inspired by [`etsy/phpunit-extensions`](https://github.com/etsy/phpunit-extensions)
