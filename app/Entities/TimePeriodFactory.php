<?php

namespace App\Entities;

class TimePeriodFactory extends TimePeriod
{

    private function __construct() {}

    public static function generate(string $dateString, string $timePeriodName): ?TimePeriod
    {
        if (self::validate($dateString, $timePeriodName)) {
            return self::prepareTimePeriodEntity($dateString, $timePeriodName);
        }
        return null;
    }


    private static function validate(string $dateString, string $timePeriodName): bool
    {
        return preg_match('/\b\d{4}-\d{2}-\d{2}\b/i', $dateString) 
            && in_array($timePeriodName, ['day', 'week', 'month', 'year']);
    }


    private static function prepareTimePeriodEntity(string $dateString, string $timePeriodName): TimePeriod
    {
        $timePeriod = new self;
        $timePeriod->setName($timePeriodName);
        return self::prepareBoundariesAndSteps($timePeriod, $dateString);
    }


    private static function prepareBoundariesAndSteps(TimePeriod $timePeriod, string $dateString): TimePeriod
    {
        $start = (new \DateTime($dateString))->setTime(0, 0);
        $end = (new \DateTime($dateString))->setTime(23, 0);
        switch ($timePeriod->getName()) {
            case 'day':
                $timePeriod->setDisplayName($start->format('Y-m-d')); 
                break;
            case 'week':
                $timePeriod->setDisplayName("Week {$start->format('W')}/{$start->format('Y')}");
                $start->modify('-' . intval($start->format('N')) - 1 . ' days');
                $end->modify('+' . 7 - intval($end->format('N')) . ' days');
                break;
            case 'month':
                $timePeriod->setDisplayName($start->format('m').'/'.$start->format('Y'));
                $start->modify('-' . intval($start->format('j')) - 1 . ' days');
                $end->modify('+' . intval($end->format('t')) - intval($end->format('j')) . ' days');
                break;
            case 'year':
                $timePeriod->setDisplayName($start->format('Y'));
                $start = new \DateTime($start->format('Y') . '-01-01 00:00');
                $end = new \DateTime($end->format('Y') . '-12-31 23:00');
        }
        $timePeriod->setStart(\DateTimeImmutable::createFromMutable($start));
        $timePeriod->setEnd(\DateTimeImmutable::createFromMutable($end));
        return self::prepareSteps($timePeriod);
    }


    private static function prepareSteps(TimePeriod $timePeriod): TimePeriod
    {
        $currentDate = \DateTime::createFromImmutable($timePeriod->getStart());
        $periodStepModification = self::getPeriodStepModification($timePeriod->getName());
        while ($currentDate->getTimestamp() < $timePeriod->getEnd()->getTimestamp()) {
            $stepStart = $currentDate->format('Y-m-d H:i');
            $currentDate->modify($periodStepModification);
            $endDate = $currentDate->format('Y-m-d H:i');
            // Step must not exceed the period by more than one day
            if ($currentDate->getTimestamp() > $timePeriod->getEnd()->getTimestamp()+24*60*60) {
                $endDate = $timePeriod->getEnd()->format('Y-m-d 00:00');
            }
            $timePeriod->addStep([$stepStart, $endDate]);
        }
        return $timePeriod;
    }


    private static function getPeriodStepModification(string $timePeriodName): string
    {
        switch ($timePeriodName) {
            case 'day':
                return '+1 hour';
            case 'week':
                return '+1 day';
            case 'month':
                return '+1 week';
            case 'year':
                return '+1 month';
        }
    }
}