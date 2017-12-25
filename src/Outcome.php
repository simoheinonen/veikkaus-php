<?php

namespace Dudgeon\Veikkaus;

class Outcome
{
    private $home;
    private $away;

    public function getHome(): Team
    {
        return $this->home;
    }

    public function getAway(): Team
    {
        return $this->away;
    }
}

