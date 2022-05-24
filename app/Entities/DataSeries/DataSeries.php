<?php

namespace App\Entities\DataSeries;

use App\Entities\TimePeriod\TimePeriod;

abstract class DataSeries
{

    private array $values = [];
    private array $fields =[];


    protected function __construct(array $fields)
    {
        $this->fields = $fields;
    }


    public function getValues(): array
    {
        return $this->values;
    }


    protected function addDataPoint(object $dataPoint): void
    {
        if (isset($dataPoint->datetime)) {
            foreach ($this->fields as $field) {
                $this->addFieldToValues($dataPoint, $field);
            }
        }
    }


    protected function accumulateData(TimePeriod $timePeriod): void
    {
        $result = [];
        foreach ($this->fields as $fieldName) {
            $result[$fieldName] = [];
            foreach ($timePeriod->getSteps() as $periodStep) {
                $result[$fieldName][] = $this->calculateMeanValueOfPeriodStep($fieldName, $periodStep);
            }
        }
        $this->values = $result;
    }


    private function calculateMeanValueOfPeriodStep(string $fieldName, array $periodStep): array
    {
        $stepCount = $stepSum = 0;
        foreach ($this->values[$fieldName] as $dataPoint) {
            $pointTime = strtotime($dataPoint['dt']);
            if ($periodStep[0]->getTimestamp() <= $pointTime && $periodStep[1]->getTimestamp() > $pointTime) {
                ++$stepCount;
                $stepSum += floatval($dataPoint['value']);
            }
        }
        return [
            'dt' => $periodStep[0]->format('Y-m-d H:i'),
            'value' => $stepCount > 0 ? round($stepSum / $stepCount, 2) : 0
        ];
    }



    private function addFieldToValues(object $dataPoint, string $field): void
    {
        if (!isset($dataPoint->$field)) {
            return; 
        }
        if (!isset($this->values[$field])) {
            $this->values[$field] = [];
        }
        $this->values[$field][] = [
            'dt' => $dataPoint->datetime,
            'value' => $dataPoint->$field
        ];
    }

}