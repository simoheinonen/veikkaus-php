<?php

namespace Dudgeon\Veikkaus;

class Rows implements \IteratorAggregate, \Countable
{
    private $rows = [];

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->rows);
    }

    public function count(): int
    {
        return count($this->rows);
    }
}
