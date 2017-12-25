<?php

namespace Dudgeon\Veikkaus;

class Row
{
    private $id;
    private $tvChannel;
    private $eventId;
    private $additionalPrizeTier;
    private $outcome;

    public function getOutcome(): Outcome
    {
        return $this->outcome;
    }
}
