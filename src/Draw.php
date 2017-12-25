<?php

namespace Dudgeon\Veikkaus;

class Draw
{
    private $gameName;
    private $id;
    private $name = '';
    private $brandName;
    private $status;
    private $openTime;
    private $closeTime;
    private $drawTime;
    private $resultsAvailableTime;
    private $gameRuleSet;
    private $rows = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getCloseTime(): \DateTime
    {
        return $this->closeTime;
    }
}

