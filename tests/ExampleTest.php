<?php

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
