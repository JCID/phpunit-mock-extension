<?php

namespace PHPUnit\Extensions\MockObject\Stub\ReturnMapping;

class EntryBuilder
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
     * @var \PHPUnit_Framework_MockObject_Stub|null
     */
    private $return;

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $matcher
     */
    public function __construct(\PHPUnit_Framework_MockObject_Matcher_Invocation $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @return $this
     */
    public function with()
    {
        $args = func_get_args();

        $this->parameters = new \PHPUnit_Framework_MockObject_Matcher_Parameters($args);

        return $this;
    }

    /**
     * @param  \PHPUnit_Framework_MockObject_Stub $stub
     *
     * @return $this
     */
    public function will(\PHPUnit_Framework_MockObject_Stub $stub)
    {
        $this->return = $stub;

        return $this;
    }

    /**
     * @param  mixed $value
     * @param  mixed $nextValues , ...
     *
     * @return $this
     */
    public function willReturn($value, ...$nextValues)
    {
        $stub = count($nextValues) === 0 ?
            new \PHPUnit_Framework_MockObject_Stub_Return($value) :
            new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls(
                array_merge([$value], $nextValues)
            );

        return $this->will($stub);
    }

    /**
     * @param  array $valueMap
     *
     * @return $this
     */
    public function willReturnMap(array $valueMap)
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_ReturnValueMap(
            $valueMap
        );

        return $this->will($stub);
    }

    /**
     * @param  mixed $argumentIndex
     *
     * @return $this
     */
    public function willReturnArgument($argumentIndex)
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_ReturnArgument(
            $argumentIndex
        );

        return $this->will($stub);
    }

    /**
     * @param  callable $callback
     *
     * @return $this
     */
    public function willReturnCallback($callback)
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_ReturnCallback(
            $callback
        );

        return $this->will($stub);
    }

    /**
     * @return $this
     */
    public function willReturnSelf()
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_ReturnSelf;

        return $this->will($stub);
    }

    /**
     * @param  mixed $values , ...
     *
     * @return $this
     */
    public function willReturnOnConsecutiveCalls(...$values)
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($values);

        return $this->will($stub);
    }

    /**
     * @param  \Exception $exception
     *
     * @return $this
     */
    public function willThrowException(\Exception $exception)
    {
        $stub = new \PHPUnit_Framework_MockObject_Stub_Exception($exception);

        return $this->will($stub);
    }

    /**
     * @return Entry
     */
    public function build()
    {
        return new Entry($this->matcher, $this->parameters, $this->return);
    }
}

