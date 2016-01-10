<?php

namespace PHPUnit\Extensions\MockObject\Stub\Tests;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Builder;

class CorrectPathTest extends \PHPUnit_Framework_TestCase
{
    public function testPath()
    {
        $builder = new Builder();
        $builder->expects($this->exactly(2))
            ->with('first method call matcher');
        $builder->expects($this->once())
            ->with('second method call matcher');
        $builder->expects($this->exactly(2))
            ->with('third method call matcher');
        $matcher = $builder->matcher();

        $invocation = $this->createInvocation(['first method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['first method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['second method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['third method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['third method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $matcher->verify();
        $this->assertSame('', $matcher->toString());
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
