<?php

namespace Tests\Unit\Entities;

use App\Entities\DataSeries\DataSeries;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriodFactory;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DataSeriesTest extends TestCase
{

    private Collection $periodWeatherData;

    public function test_daily_weather_data()
    {
        $dataSeries = DataSeriesFactory::generate(
            $this->periodWeatherData, 
            ['temperature', 'wind', 'clouds', 'rain', 'snow'], 
            TimePeriodFactory::generate('2022-01-01', 'day')
        )->getValues();

        $this->assertCount(24, $dataSeries['temperature']);
        $this->assertCount(24, $dataSeries['wind']);
        $this->assertCount(24, $dataSeries['clouds']);
        $this->assertCount(24, $dataSeries['rain']);
        $this->assertCount(24, $dataSeries['snow']);

        $this->assertEquals('2022-01-01 00:00', $dataSeries['temperature'][0]['dt']);
        $this->assertEquals(0.97, $dataSeries['rain'][4]['value']);
        $this->assertEquals(2.17, $dataSeries['wind'][2]['value']);
        //$this->assertEquals(0, $dataSeries['rain'][6]['value']);
    }


    public function test_weekly_weather_data()
    {
        $dataSeries = DataSeriesFactory::generate(
            $this->periodWeatherData, 
            ['temperature', 'wind', 'clouds', 'rain', 'snow'], 
            TimePeriodFactory::generate('2022-01-01', 'week')
        )->getValues();

        $this->assertCount(2, $dataSeries['temperature']);
        $this->assertCount(2, $dataSeries['wind']);
        $this->assertCount(2, $dataSeries['clouds']);
        $this->assertCount(2, $dataSeries['rain']);
        $this->assertCount(2, $dataSeries['snow']);

        $this->assertEquals('2022-01-01 00:00', $dataSeries['temperature'][0]['dt']);
        $this->assertEquals(0.93, $dataSeries['rain'][0]['value']);
        /*$this->assertEquals(2.17, $dataSeries['wind'][2]['value']);
        $this->assertEquals(0, $dataSeries['rain'][6]['value']);*/
    }


    public function setUp(): void
    {
        $this->periodWeatherData = collect([
            // 01.01.22
            (object)['datetime' => '2022-01-01 00:00', 'temperature' => 2.74, 'wind' => 2.52, 'clouds' => 32.48, 'rain' => 0.49, 'snow' => 0.04],
            (object)['datetime' => '2022-01-01 01:00', 'temperature' => 7.38, 'wind' => 0.93, 'clouds' => 64.33, 'rain' => 1.04, 'snow' => 1.91],
            (object)['datetime' => '2022-01-01 02:00', 'temperature' => 1.91, 'wind' => 2.17, 'clouds' => 97.86, 'rain' => 1.24, 'snow' => 0.83],
            (object)['datetime' => '2022-01-01 03:00', 'temperature' => 6.03, 'wind' => 4.9, 'clouds' => 84.92, 'rain' => 0.89, 'snow' => 1.28],
            (object)['datetime' => '2022-01-01 04:00', 'temperature' => 6.24, 'wind' => 6.2, 'clouds' => 64.03, 'rain' => 0.97, 'snow' => 1.36],
            // 02.01.22
            (object)['datetime' => '2022-01-02 00:00', 'temperature' => 1.8, 'wind' => 7.13, 'clouds' => 92.17, 'rain' => 0.6, 'snow' => 1.46],
            (object)['datetime' => '2022-01-02 01:00', 'temperature' => 1.13, 'wind' => 5.88, 'clouds' => 52.73, 'rain' => 0.77, 'snow' => 1.04],
            (object)['datetime' => '2022-01-02 02:00', 'temperature' => 2.29, 'wind' => 3.94, 'clouds' => 81.42, 'rain' => 1.24, 'snow' => 1.05],
            // 03.01.22
            (object)['datetime' => '2022-01-03 00:00', 'temperature' => 4.25, 'wind' => 0.73, 'clouds' => 90.98, 'rain' => 0.65, 'snow' => 0.61],
            (object)['datetime' => '2022-01-03 01:00', 'temperature' => 6.65, 'wind' => 5.83, 'clouds' => 79.09, 'rain' => 0.13, 'snow' => 1.15],
            // 04.01.22
            (object)['datetime' => '2022-01-04 00:00', 'temperature' => 7.14, 'wind' => 2.92, 'clouds' => 60.17, 'rain' => 0.9, 'snow' => 1.01],
            (object)['datetime' => '2022-01-04 01:00', 'temperature' => 2.76, 'wind' => 2.31, 'clouds' => 52.18, 'rain' => 0.21, 'snow' => 0.36],
            // 05.01.22
            (object)['datetime' => '2022-01-05 00:00', 'temperature' => 5.11, 'wind' => 6.66, 'clouds' => 61.33, 'rain' => 0.03, 'snow' => 0.19],
            (object)['datetime' => '2022-01-05 01:00', 'temperature' => 4.46, 'wind' => 4.24, 'clouds' => 44.77, 'rain' => 1.93, 'snow' => 0.7],
            // 01.02.22
            (object)['datetime' => '2022-02-01 00:00', 'temperature' => 0.45, 'wind' => 3.33, 'clouds' => 59.32, 'rain' => 1.55, 'snow' => 0.3],
            (object)['datetime' => '2022-02-01 01:00', 'temperature' => 5.3, 'wind' => 6.24, 'clouds' => 36.48, 'rain' => 0.44, 'snow' => 1.18],
            // 02.02.22
            (object)['datetime' => '2022-02-02 00:00', 'temperature' => 1.47, 'wind' => 6.57, 'clouds' => 10.74, 'rain' => 1.57, 'snow' => 1.05],
            (object)['datetime' => '2022-02-02 01:00', 'temperature' => 3.57, 'wind' => 4.04, 'clouds' => 75.37, 'rain' => 0.05, 'snow' => 0.02],
            // 01.03.22
            (object)['datetime' => '2022-03-01 00:00', 'temperature' => 2.9, 'wind' => 5.94, 'clouds' => 74.7, 'rain' => 1.19, 'snow' => 1.5],
            (object)['datetime' => '2022-03-01 01:00', 'temperature' => 5.83, 'wind' => 2.71, 'clouds' => 45.98, 'rain' => 0.62, 'snow' => 1.89],
            // 15.03.22
            (object)['datetime' => '2022-03-15 00:00', 'temperature' => 6.73, 'wind' => 5.22, 'clouds' => 19.43, 'rain' => 1.59, 'snow' => 0.6],
            (object)['datetime' => '2022-03-15 01:00', 'temperature' => 5.32, 'wind' => 7.04, 'clouds' => 88.6, 'rain' => 0.21, 'snow' => 1.8],
            // 01.04.22
            (object)['datetime' => '2022-04-01 00:00', 'temperature' => 3.1, 'wind' => 2.37, 'clouds' => 9.48, 'rain' => 0.27, 'snow' => 0.86],
            (object)['datetime' => '2022-04-01 01:00', 'temperature' => 7, 'wind' => 4, 'clouds' => 10, 'rain' => 0, 'snow' => 0]
        ]);
    }

}