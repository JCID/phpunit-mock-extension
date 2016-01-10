<?php

namespace PHPUnit\Extensions\MockObject\Stub\Tests;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Builder;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testDummy()
    {
        $builder = new Builder();
        $builder->expects($this->exactly(2))
            ->with('first method call matcher')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->with('second method call matcher')
            ->willReturn('second return');
        $builder->expects($this->exactly(2))
            ->with('third method call matcher')
            ->willReturn('third return');

        $dummy = $this->getMock(\stdClass::class, ['test', 'random']);
        $dummy->expects($builder->matcher())->method('test')->will($builder->mapper());
        $dummy->expects($this->once())->method('random')->with('random other method call')->willReturn('random other method return');

        $this->assertSame($dummy, $dummy->test('first method call matcher'));
        $this->assertSame($dummy, $dummy->test('first method call matcher'));
        $this->assertSame('second return', $dummy->test('second method call matcher'));
        $this->assertSame('third return', $dummy->test('third method call matcher'));
        $this->assertSame('third return', $dummy->test('third method call matcher'));

        $this->assertSame('random other method return', $dummy->random('random other method call'));
    }

    /**
     * @expectedException        \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage wrong argument
     */
    public function testWrongArguments()
    {
        $builder = new Builder();
        $builder->expects($this->exactly(2))
            ->with('first method call matcher')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->with('second method call matcher')
            ->willReturn('second return');

        $dummy = $this->getMock(\stdClass::class, ['test']);
        $dummy->expects($builder->matcher())->method('test')->will($builder->mapper());

        $this->assertSame($dummy, $dummy->test('first method call matcher'));
        $this->assertSame($dummy, $dummy->test('first method call matcher'));
        $this->assertSame('second return', $dummy->test('wrong argument'));
    }
}
