<?php

namespace App\Entities\TimePeriod;

class TimePeriodFactory extends TimePeriod
{

    private function __construct() {}

    public static function generate(string $dateString, string $timePeriodName): ?TimePeriod
    {
        if (self::validate($dateString, $timePeriodName)) {
            try {
                return self::prepareTimePeriodEntity($dateString, $timePeriodName);
            } catch (\Exception $e) {
                return null;
            }
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
        $end = (clone $start)->modify('+1 day');
        self::addDayProperties($timePeriod, $start, $end);
        self::addWeekProperties($timePeriod, $start, $end);
        self::addMonthProperties($timePeriod, $start, $end);
        self::addYearProperties($timePeriod, $start, $end);
        return self::prepareSteps($timePeriod);
    }


    private static function addDayProperties(TimePeriod $timePeriod, \DateTime $start): void
    {
        if ($timePeriod->getName() === 'day') {
            $timePeriod->setDisplayName($start->format('Y-m-d'));
            $timePeriod->setStart(self::dt2dti($start));
            $timePeriod->setEnd(self::dt2dti((clone $start)->modify('+1 day')));
        }
    }


    private static function addWeekProperties(TimePeriod $timePeriod, \DateTime $start): void
    {
        if ($timePeriod->getName() === 'week') {
            $end = (clone $start)->modify('+1 day');
            $timePeriod->setStart(self::dt2dti($start->modify('-' . intval($start->format('N')) - 1 . ' days')));
            $timePeriod->setEnd(self::dt2dti($end->modify('+' . 8 - intval($end->format('N')) . ' days')));
            $timePeriod->setDisplayName("Week {$timePeriod->getStart()->format('W')}/{$timePeriod->getStart()->format('Y')}");
        }
    }


    private static function addMonthProperties(TimePeriod $timePeriod, \DateTime $start): void
    {
        if ($timePeriod->getName() === 'month') {
            $timePeriod->setDisplayName($start->format('m').'/'.$start->format('Y'));
            $firstDayOfMonth = new \DateTime($start->format('Y-m-01'));
            $lastDayOfMonth = new \DateTime($start->format('Y-m-' . $start->format('t')));
            $timePeriod->setStart(self::dt2dti($firstDayOfMonth->modify('-' . intval($firstDayOfMonth->format('N')) - 1 . ' days')));
            $timePeriod->setEnd(self::dt2dti($lastDayOfMonth->modify('+' . 8 - intval($lastDayOfMonth->format('N')) . ' days')));
        }
    }


    private static function addYearProperties(TimePeriod $timePeriod, \DateTime $start): void
    {
        if ($timePeriod->getName() === 'year') {
            $timePeriod->setDisplayName($start->format('Y'));
            $timePeriod->setStart(new \DateTimeImmutable($start->format('Y') . '-01-01 00:00'));
            $timePeriod->setEnd(new \DateTimeImmutable($start->modify('+1 year')->format('Y') . '-01-01 00:00'));
        }
    }


    private static function dt2dti(\DateTime $dt): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($dt);
    }


    private static function prepareSteps(TimePeriod $timePeriod): TimePeriod
    {
        $currentDate = \DateTime::createFromImmutable($timePeriod->getStart());
        $periodStepModification = self::getPeriodStepModification($timePeriod->getName());
        while ($currentDate->getTimestamp() < $timePeriod->getEnd()->getTimestamp()) {
            $stepStart = \DateTimeImmutable::createFromMutable($currentDate);
            $currentDate->modify($periodStepModification);
            $endDate = \DateTimeImmutable::createFromMutable($currentDate);
            // Step must not exceed the period by more than one day
            /*if ($currentDate->getTimestamp() > $timePeriod->getEnd()->getTimestamp()+24*60*60) {
                $endDate = new \DateTimeImmutable($timePeriod->getEnd()->format('Y-m-d 00:00'));
            }*/
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