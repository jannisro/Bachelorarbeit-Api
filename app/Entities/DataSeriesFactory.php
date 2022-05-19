<?php

namespace App\Entities;

class DataSeriesFactory extends DataSeries
{

    private function __construct() {}

    public static function generate(string $modelName, Country $country, TimePeriod $timePeriod): DataSeries
    {
        return (new self())
            ->setCountry($country)
            ->setTimePeriod($timePeriod)
            ->setSeries(self::prepareSeriesData($modelName, $country, $timePeriod));
    }


    private static function prepareSeriesData(string $modelName, Country $country, TimePeriod $timePeriod): array
    {
        $result = [];
        foreach ($timePeriod->getSteps() as $step) {
            $result[] = $modelName::select(['dt', 'value'])
                ->where('country', $country->getCode())
                ->where('dt', '>=', $step[0])
                ->where('dt', '<', $step[1])
                ->get();
        }
        return [];
    }

}