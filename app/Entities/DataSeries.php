<?php

namespace App\Entities;

abstract class DataSeries
{

    private TimePeriod $timePeriod;
    private Country $country;
    private array $series;

    public function getTimePeriod(): TimePeriod
    {
        return $this->timePeriod;
    }

    protected function setTimePeriod(TimePeriod $period): DataSeries
    {
        $this->timePeriod = $period;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    protected function setCountry(Country $country): DataSeries
    {
        $this->country = $country;
        return $this;
    }

    public function getSeries(): array
    {
        return $this->series;
    }

    protected function setSeries(array $series): DataSeries
    {
        $this->series = $series;
        return $this;
    }

}