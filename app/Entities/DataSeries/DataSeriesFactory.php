<?php

namespace App\Entities\DataSeries;

use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Support\Collection;

class DataSeriesFactory extends DataSeries
{


    public static function generate(Collection $periodData, array $fields, TimePeriod $timePeriod): DataSeries
    {
        $hourlyDataSeries = new self($fields);
        foreach ($periodData as $dataPoint) {
            $hourlyDataSeries->addDataPoint($dataPoint);
        } 
        if ($timePeriod->getName() !== 'day') {
            $hourlyDataSeries->accumulateData($timePeriod);
        }
        return $hourlyDataSeries;
    }

}