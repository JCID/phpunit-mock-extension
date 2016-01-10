<?php

namespace PHPUnit\Extensions\MockObject\Stub\ReturnMapping;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping;
use PHPUnit\Extensions\MockObject\Stub\VerifyMatcher;

class Builder
{
    /**
     * @var EntryBuilder[]
     */
    private $builders = [];

    /**
     * @var VerifyMatcher
     */
    private $matcher;

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher
     *
     * @return EntryBuilder
     */
    public function expects(\PHPUnit_Framework_MockObject_Matcher_Invocation $matcher)
    {
        return $this->builders[] = new EntryBuilder($matcher);
    }

    /**
     * @return VerifyMatcher
     */
    public function matcher()
    {
        if ($this->matcher) {
            return $this->matcher;
        }

        $entries = [];
        foreach ($this->builders as $builder) {
            $entries[] = $builder->build();
        }

        return $this->matcher = new VerifyMatcher($entries);
    }

    /**
     * @return ReturnMapping
     */
    public function mapper()
    {
        return new ReturnMapping($this->matcher());
    }
}

