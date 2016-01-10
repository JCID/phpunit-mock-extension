<?php

namespace PHPUnit\Extensions\MockObject\Stub;

use PHPUnit\Extensions\MockObject\Stub\ReturnMapping\Entry;
use PHPUnit_Framework_MockObject_Invocation;
use PHPUnit_Framework_MockObject_Matcher_Invocation;

class VerifyMatcher implements PHPUnit_Framework_MockObject_Matcher_Invocation
{
    /**
     * @var ReturnMapping\Entry[]
     */
    private $entries;

    /**
     * @var int
     */
    private $currentIndex = 0;

    /**
     * @param \Iterator|Entry[] $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = array_values($entries);
    }

    /**
     * {@inheritdoc}
     */
    public function invoked(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        $entry = $this->getCurrentEntry();

        try {
            $entry->match($invocation);
        } catch (\PHPUnit_Framework_ExpectationFailedException $keepLast) {
            $this->currentIndex++;
            $this->invoked($invocation);

            return;
        }

        $entry->validate($invocation);
    }

    /**
     * @return Entry
     */
    public function getCurrentEntry()
    {
        if (!isset($this->entries[$this->currentIndex])) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'The invocation at index %s is not defined.',
                    $this->currentIndex
                )
            );
        }

        return $this->entries[$this->currentIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function matches(PHPUnit_Framework_MockObject_Invocation $invocation)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function verify()
    {
        $this->getCurrentEntry()->verify();
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return '';
    }
}