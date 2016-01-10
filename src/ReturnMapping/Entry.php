<?php

namespace PHPUnit\Extensions\MockObject\Stub\ReturnMapping;

class Entry
{
    /**
     * @var \PHPUnit_Framework_MockObject_Matcher_Invocation
     */
    private $matcher;

    /**
     * @var \PHPUnit_Framework_MockObject_Matcher_Parameters|null
     */
    private $parameters;

    /**
     * @var \PHPUnit_Framework_MockObject_Stub|mixed|null
     */
    private $return;

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation      $matcher
     * @param \PHPUnit_Framework_MockObject_Matcher_Parameters|null $parameters
     * @param \PHPUnit_Framework_MockObject_Stub|mixed|null         $return
     */
    public function __construct(
        \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher,
        \PHPUnit_Framework_MockObject_Matcher_Parameters $parameters = null,
        $return = null
    )
    {
        $this->matcher    = $matcher;
        $this->parameters = $parameters;
        $this->return     = $return;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation $invocation
     */
    public function match(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        $this->matcher->invoked($invocation);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation $invocation
     */
    public function validate(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if ($this->parameters) {
            $this->parameters->matches($invocation);
        }
    }

    public function invoke(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        if ($this->return instanceof \PHPUnit_Framework_MockObject_Stub) {
            return $this->return->invoke($invocation);
        }
        return $this->return;
    }

    /**
     * Verifies that the current expectation is valid. If everything is OK the
     * code should just return, if not it must throw an exception.
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function verify()
    {
        $this->matcher->verify();
    }
}

