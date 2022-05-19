<?php

namespace Tests\Unit\Entities;

use App\Entities\TimePeriodFactory;
use PHPUnit\Framework\TestCase;

class TimePeriodTest extends TestCase
{
    
    public function test_day_period()
    {
        $timePeriod = TimePeriodFactory::generate(date('Y-m-d'), 'day');
        $this->assertCount(23, $timePeriod->getSteps());
        $this->assertEquals(
            [date('Y-m-d 00:00'), date('Y-m-d 01:00')], 
            $timePeriod->getSteps()[0]
        );
        $this->assertEquals(
            [date('Y-m-d 22:00'), date('Y-m-d 23:00')], 
            $timePeriod->getSteps()[22]
        );
        $this->assertEquals(
            date('Y-m-d 00:00'), 
            $timePeriod->getStart()->format('Y-m-d H:i')
        );
        $this->assertEquals(
            date('Y-m-d 23:00'), 
            $timePeriod->getEnd()->format('Y-m-d H:i')
        );
        $this->assertEquals('day', $timePeriod->getName());
        $this->assertEquals(
            date('Y-m-d'), 
            $timePeriod->getDisplayName()
        );
    }
    
    public function test_week_period()
    {
        $this->runWeekPeriodAssertions('2022-05-16');
        $this->runWeekPeriodAssertions('2022-05-17');
        $this->runWeekPeriodAssertions('2022-05-18');
        $this->runWeekPeriodAssertions('2022-05-19');
        $this->runWeekPeriodAssertions('2022-05-20');
        $this->runWeekPeriodAssertions('2022-05-21');
        $this->runWeekPeriodAssertions('2022-05-22');
    }

    private function runWeekPeriodAssertions(string $startDate): void
    {
        $timePeriod = TimePeriodFactory::generate($startDate, 'week');
        $this->assertCount(7, $timePeriod->getSteps());
        $this->assertEquals(['2022-05-16 00:00','2022-05-17 00:00'], $timePeriod->getSteps()[0]);
        $this->assertEquals(['2022-05-17 00:00','2022-05-18 00:00'], $timePeriod->getSteps()[1]);
        $this->assertEquals(['2022-05-18 00:00','2022-05-19 00:00'], $timePeriod->getSteps()[2]);
        $this->assertEquals(['2022-05-22 00:00', '2022-05-23 00:00'], $timePeriod->getSteps()[6]);
        $this->assertEquals('2022-05-16 00:00', $timePeriod->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2022-05-22 23:00', $timePeriod->getEnd()->format('Y-m-d H:i'));
        $this->assertEquals('week', $timePeriod->getName());
        $this->assertEquals('Week 20/2022', $timePeriod->getDisplayName());
    }
    
    public function test_month_period()
    {
        $timePeriod = TimePeriodFactory::generate('2022-05-19', 'month');
        $this->assertCount(5, $timePeriod->getSteps());
        $this->assertEquals(['2022-05-01 00:00', '2022-05-08 00:00'], $timePeriod->getSteps()[0]);
        $this->assertEquals(['2022-05-08 00:00', '2022-05-15 00:00'], $timePeriod->getSteps()[1]);
        $this->assertEquals(['2022-05-15 00:00', '2022-05-22 00:00'], $timePeriod->getSteps()[2]);
        $this->assertEquals(['2022-05-22 00:00', '2022-05-29 00:00'], $timePeriod->getSteps()[3]);
        $this->assertEquals(['2022-05-29 00:00', '2022-05-31 00:00'], $timePeriod->getSteps()[4]);
        $this->assertEquals('2022-05-01 00:00', $timePeriod->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2022-05-31 23:00', $timePeriod->getEnd()->format('Y-m-d H:i'));
        $this->assertEquals('month', $timePeriod->getName());
        $this->assertEquals('05/2022', $timePeriod->getDisplayName());
    }
    
    public function test_year_period()
    {
        $timePeriod = TimePeriodFactory::generate('2022-05-19', 'year');
        $this->assertCount(12, $timePeriod->getSteps());
        $this->assertEquals(['2022-01-01 00:00', '2022-02-01 00:00'], $timePeriod->getSteps()[0]);
        $this->assertEquals(['2022-02-01 00:00', '2022-03-01 00:00'], $timePeriod->getSteps()[1]);
        $this->assertEquals(['2022-03-01 00:00', '2022-04-01 00:00'], $timePeriod->getSteps()[2]);
        $this->assertEquals(['2022-04-01 00:00', '2022-05-01 00:00'], $timePeriod->getSteps()[3]);
        $this->assertEquals(['2022-05-01 00:00', '2022-06-01 00:00'], $timePeriod->getSteps()[4]);
        $this->assertEquals('2022-01-01 00:00', $timePeriod->getStart()->format('Y-m-d H:i'));
        $this->assertEquals('2022-12-31 23:00', $timePeriod->getEnd()->format('Y-m-d H:i'));
        $this->assertEquals('year', $timePeriod->getName());
        $this->assertEquals('2022', $timePeriod->getDisplayName());
    }

    public function test_invalid_date_string()
    {
        $this->assertNull(TimePeriodFactory::generate('22.05.2019', 'day'));
        $this->assertNull(TimePeriodFactory::generate('xxx', 'day'));
        $this->assertNull(TimePeriodFactory::generate('22-05-01', 'day'));
        $this->assertNull(TimePeriodFactory::generate('29812-01-01', 'day'));
        $this->assertNull(TimePeriodFactory::generate('20220501', 'day'));
    }

    public function test_invalid_time_period_name()
    {
        $this->assertNull(TimePeriodFactory::generate('2022-01-01', 'foo'));
        $this->assertNull(TimePeriodFactory::generate('2022-01-01', 'yea'));
        $this->assertNull(TimePeriodFactory::generate('2022-01-01', 'dy'));
        $this->assertNull(TimePeriodFactory::generate('2022-01-01', 'weeek'));
    }

}
