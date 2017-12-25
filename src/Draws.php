<?php

namespace Dudgeon\Veikkaus;

class Draws implements \IteratorAggregate, \Countable
{
    private $draws = [];

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->draws);
    }

    public function count(): int
    {
        return count($this->draws);
    }
}
