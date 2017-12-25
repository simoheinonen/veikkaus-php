<?php

namespace Dudgeon\Veikkaus;

use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;

class DateTimeHandler extends DateHandler
{
    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if ((string) $data === '') {
            return null;
        }

        return new \DateTime('@' . (int) ($data/1000));
    }
}
