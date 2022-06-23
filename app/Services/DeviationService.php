<?php

namespace App\Services;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeviationService
{

    /**
     * Calculates deviation of data points from the long-term mean
     * 
     * @param TimePeriod $timePeriod Current time period. Mean of this period id compared to trend
     * @param Country $country
     * @param string $modelName FQCN (name incl. namespace) of the respective Model
     * @param array $modelFields Column names to calculate the deviation for
     * @return array [modelField => deviation, modelField2 => deviation]
     */
    public static function calculate(TimePeriod $timePeriod, Builder $builder, array $modelFields): array
    {
        $timePeriodMean = self::meanOfTimePeriod($timePeriod, clone $builder, $modelFields);
        $longTermMean = self::longTermMean($timePeriod, clone $builder, $modelFields);
 
        $result = [];
        foreach ($modelFields as $modelField) {
            if (!is_null($timePeriodMean) && !is_null($longTermMean)) {
                if ($longTermMean->$modelField == 0 && $timePeriodMean->$modelField == 0) {
                    $result[$modelField] = 0;
                }
                else if ($longTermMean->$modelField == 0) {
                    $result[$modelField] = round((($timePeriodMean->$modelField / 0.01) - 1) * 100, 2);
                }
                else {
                    $result[$modelField] = round((($timePeriodMean->$modelField / $longTermMean->$modelField) - 1) * 100, 2);
                }
            }
            else {
                $result[$modelField] = 0;
            }
        }

        return $result;
    }



    private static function meanOfTimePeriod(TimePeriod $timePeriod, Builder $builder, array $modelFields): ?Model
    {
        $builder->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
                ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'));
        
        foreach ($modelFields as $field) {
            $builder->selectRaw("AVG(`$field`) AS `$field`");
        }
        return ($builder && $builder->get()->count() === 1) 
            ? $builder->first() 
            : null;
    }



    private static function longTermMean(TimePeriod $timePeriod, Builder $builder, array $modelFields): ?Model
    {
        $periodStart = \DateTime::createFromImmutable($timePeriod->getStart());

        if ($timePeriod->getName() === 'day') {
            $builder->where('datetime', '>=', $periodStart->modify('-1 month')->format('Y-m-d H:i'))
                    ->where('datetime', '<', $timePeriod->getStart()->format('Y-m-d H:i'));
        }
        else if ($timePeriod->getName() === 'month') {
            $builder->where('datetime', '>=', $periodStart->modify('-3 months')->format('Y-m-d H:i'))
                    ->where('datetime', '<', $timePeriod->getStart()->format('Y-m-d H:i'));
        }
        else if ($timePeriod->getName() === 'year') {
            $builder->where('datetime', '>=', $periodStart->modify('-3 years')->format('Y-m-d H:i'))
                    ->where('datetime', '<', $timePeriod->getStart()->format('Y-m-d H:i'));
        }

        foreach ($modelFields as $field) {
            $builder->selectRaw("AVG(`$field`) AS `$field`");
        }

        return ($builder && $builder->get()->count() === 1) 
            ? $builder->first() 
            : null;
    }

}