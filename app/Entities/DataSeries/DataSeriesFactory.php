<?php

namespace App\Entities\DataSeries;

use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Support\Collection;

class DataSeriesFactory extends DataSeries
{


    public static function generate(Collection $periodData, array $fields, TimePeriod $timePeriod): DataSeries
    {
        $dataSeries = new self($fields);
        foreach ($periodData as $dataPoint) {
            $dataSeries->addDataPoint($dataPoint);
        }
        if ($timePeriod->getName() !== 'day') {
            $dataSeries->accumulateData($timePeriod);
        }
        return $dataSeries;
    }

}