<?php

namespace Dudgeon\Veikkaus;

class Team
{
    private $id;
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}