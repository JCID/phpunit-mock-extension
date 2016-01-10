<?php

namespace PHPUnit\Extensions\MockObject\Stub\Tests;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Builder;

class ToMuchCallsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage The invocation at index 2 is not defined.
     */
    public function testPath()
    {
        $builder = new Builder();
        $builder->expects($this->once())
            ->with('first method call matcher');
        $builder->expects($this->once())
            ->with('second method call matcher');
        $matcher = $builder->matcher();

        $invocation = $this->createInvocation(['first method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['second method call matcher']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $invocation = $this->createInvocation(['random']);
        $matcher->matches($invocation);
        $matcher->invoked($invocation);

        $matcher->verify();
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
