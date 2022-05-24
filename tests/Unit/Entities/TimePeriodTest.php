<?php

namespace Tests\Unit\Entities;

use App\Entities\TimePeriod\TimePeriodFactory;
use PHPUnit\Framework\TestCase;

class TimePeriodTest extends TestCase
{

    /**
     * Test valid periods
     * @dataProvider validPeriodProvider
     */
    public function test_valid_periods(
        string $constructionDate, 
        string $periodName, 
        string $start, 
        string $end, 
        string $firstStepEnd, 
        int $stepCount, 
        string $displayName
    )
    {
        // Construction and test of item amount
        $timePeriod = TimePeriodFactory::generate($constructionDate, $periodName);
        $this->assertCount($stepCount, $timePeriod->getSteps());

        // Test start and end
        $this->assertEquals($start, $timePeriod->getStart()->format('Y-m-d H:i'));
        $this->assertEquals($end, $timePeriod->getEnd()->format('Y-m-d H:i'));

        // Test first step range
        $this->assertEquals($start, $timePeriod->getSteps()[0][0]->format('Y-m-d H:i'));
        $this->assertEquals($firstStepEnd, $timePeriod->getSteps()[0][1]->format('Y-m-d H:i'));
        $this->assertEquals($end, $timePeriod->getSteps()[$stepCount-1][1]->format('Y-m-d H:i'));

        // Test name and display name
        $this->assertEquals($periodName, $timePeriod->getName());
        $this->assertEquals($displayName, $timePeriod->getDisplayName());
    }


    public function validPeriodProvider(): array
    {
        return [
            // Day period
            ['2022-05-24', 'day', '2022-05-24 00:00', '2022-05-25 00:00', '2022-05-24 01:00', 24, '2022-05-24'],
            ['2021-12-31', 'day', '2021-12-31 00:00', '2022-01-01 00:00', '2021-12-31 01:00', 24, '2021-12-31'],
            ['2022-04-30', 'day', '2022-04-30 00:00', '2022-05-01 00:00', '2022-04-30 01:00', 24, '2022-04-30'],
            ['2022-06-15', 'day', '2022-06-15 00:00', '2022-06-16 00:00', '2022-06-15 01:00', 24, '2022-06-15'],
            ['2022-04-01', 'day', '2022-04-01 00:00', '2022-04-02 00:00', '2022-04-01 01:00', 24, '2022-04-01'],
            // Week period
            ['2022-05-24', 'week', '2022-05-23 00:00', '2022-05-30 00:00', '2022-05-24 00:00', 7, 'Week 21/2022'],
            ['2022-01-01', 'week', '2021-12-27 00:00', '2022-01-03 00:00', '2021-12-28 00:00', 7, 'Week 52/2021'],
            ['2022-04-28', 'week', '2022-04-25 00:00', '2022-05-02 00:00', '2022-04-26 00:00', 7, 'Week 17/2022'],
            ['2022-05-02', 'week', '2022-05-02 00:00', '2022-05-09 00:00', '2022-05-03 00:00', 7, 'Week 18/2022'],
            // Month period
            ['2022-05-24', 'month', '2022-04-25 00:00', '2022-06-06 00:00', '2022-05-02 00:00', 6, '05/2022'],
            ['2022-02-01', 'month', '2022-01-31 00:00', '2022-03-07 00:00', '2022-02-07 00:00', 5, '02/2022'],
            ['2022-01-23', 'month', '2021-12-27 00:00', '2022-02-07 00:00', '2022-01-03 00:00', 6, '01/2022'],
            ['2022-06-04', 'month', '2022-05-30 00:00', '2022-07-04 00:00', '2022-06-06 00:00', 5, '06/2022'],
            // Year period
            ['2022-04-29', 'year', '2022-01-01 00:00', '2023-01-01 00:00', '2022-02-01 00:00', 12, '2022'],
            ['2021-12-31', 'year', '2021-01-01 00:00', '2022-01-01 00:00', '2021-02-01 00:00', 12, '2021'],
            ['2022-07-31', 'year', '2022-01-01 00:00', '2023-01-01 00:00', '2022-02-01 00:00', 12, '2022'],
            ['2030-01-01', 'year', '2030-01-01 00:00', '2031-01-01 00:00', '2030-02-01 00:00', 12, '2030']
        ];
    }


    /**
     * Test invalid periods
     * @dataProvider invalidPeriodProvider
     */
    public function test_invalid_time_period_name(string $constructionDate, string $periodName)
    {
        $this->assertNull(TimePeriodFactory::generate($constructionDate, $periodName));
    }


    public function invalidPeriodProvider(): array
    {
        return [
            ['2022-01-01', 'foo'],
            ['2022-01-01', 'yea'],
            ['2022-01-01', 'dy'],
            ['2022-01-01', 'weeek'],
            ['20220999', 'day'],
            ['2022-01-800', 'week'],
            ['34894298493', 'month'],
            ['2022-30-05', 'year']
        ];
    }

}
