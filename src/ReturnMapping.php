<?php

namespace PHPUnit\Extensions\MockObject\Stub;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Entry;
use SebastianBergmann\Exporter\Exporter;

class ReturnMapping implements \PHPUnit_Framework_MockObject_Stub
{
    /**
     * @var VerifyMatcher
     */
    private $verifyMatcher;

    /**
     * @param VerifyMatcher $verifyMatcher
     */
    public function __construct(VerifyMatcher $verifyMatcher)
    {
        $this->verifyMatcher = $verifyMatcher;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation $invocation
     *
     * @return mixed
     */
    public function invoke(\PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        return $this->verifyMatcher->getCurrentEntry()->invoke($invocation);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return '';
    }
}


