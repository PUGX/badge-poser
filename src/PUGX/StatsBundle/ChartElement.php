<?php

namespace PUGX\StatsBundle;

class ChartElement
{
    private $datetime;
    private $value;

    function __construct(\DateTime $datetime, $value)
    {
        $this->datetime = $datetime;
        $this->value = $value;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return (int) $this->value;
    }
}