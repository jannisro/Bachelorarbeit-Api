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
        if (count($this->values) === 0) {
            foreach ($this->fields as $field) {
                $this->values[$field] = [];
            }
        }
        return $this->values;
    }


    public function formatDayLabels (): void
    {
        foreach ($this->values as $fieldName => $values) {
            foreach ($values as $index => $dataPoint) {
                $this->values[$fieldName][$index]['dt'] = date('H:i', strtotime($dataPoint['dt']));
            }
        }
    }


    protected function addDataPoint(object $dataPoint): void
    {
        if (isset($dataPoint->datetime)) {
            foreach ($this->fields as $field) {
                $this->addFieldValues($dataPoint, $field);
            }
        }
    }


    protected function accumulateData(TimePeriod $timePeriod): void
    {
        $result = [];
        foreach ($this->fields as $fieldName) {
            $result[$fieldName] = [];
            foreach ($timePeriod->getSteps() as $periodStep) {
                $result[$fieldName][] = $this->calculateMeanValueOfPeriodStep($fieldName, $periodStep, $timePeriod);
            }
        }
        $this->values = $result;
    }


    private function calculateMeanValueOfPeriodStep(string $fieldName, array $periodStep, TimePeriod $timePeriod): array
    {
        $stepCount = $stepSum = 0;
        if (isset($this->values[$fieldName])) {
            foreach ($this->values[$fieldName] as $dataPoint) {
                $pointTime = strtotime($dataPoint['dt']);
                if ($periodStep[0]->getTimestamp() <= $pointTime && $periodStep[1]->getTimestamp() > $pointTime) {
                    ++$stepCount;
                    $stepSum += floatval($dataPoint['value']);
                }
            }
        }
        return [
            'dt' => $this->getDateTimeDisplay($periodStep[0], $timePeriod),
            'value' => $stepCount > 0 ? round($stepSum / $stepCount, 2) : 0
        ];
    }


    private function addFieldValues(object $dataPoint, string $field): void
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


    private function getDateTimeDisplay(\DateTimeImmutable $dt, TimePeriod $timePeriod): string
    {
        switch ($timePeriod->getName()) {
            case 'day':
                return $dt->format('H:i');
            case 'month':
                return $dt->format('Y-m-d');
            case 'year':
                return $dt->format('m/Y');
        }
    }

}