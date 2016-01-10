<?php

namespace PHPUnit\Extensions\MockObject\Stub\Tests;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Builder;

class ReturnValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testPath()
    {
        $builder = new Builder();
        $builder->expects($this->exactly(2))
            ->with('first method call matcher')
            ->willReturn('first random-string');
        $builder->expects($this->once())
            ->with('second method call matcher')
            ->willReturn('second random-string');
        $matcher      = $builder->matcher();
        $returnMapper = $builder->mapper();

        $invocation = $this->createInvocation(['first method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);
        $this->assertSame('first random-string', $returnMapper->invoke($invocation));

        $invocation = $this->createInvocation(['first method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);
        $this->assertSame('first random-string', $returnMapper->invoke($invocation));

        $invocation = $this->createInvocation(['second method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);
        $this->assertSame('second random-string', $returnMapper->invoke($invocation));

        $matcher->verify();
        $this->assertSame('', $returnMapper->toString());
    }

    /**
     * @param array $parameters
     *
     * @return \PHPUnit_Framework_MockObject_Invocation_Object
     */
    private function createInvocation(array $parameters)
    {
        $invocation             = $this->getMockBuilder(\PHPUnit_Framework_MockObject_Invocation_Object::class)
            ->disableOriginalConstructor()
            ->getMock();
        $invocation->parameters = $parameters;

        return $invocation;
    }
}
