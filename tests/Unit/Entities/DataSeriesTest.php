<?php

namespace Tests\Unit\Entities;

use App\Entities\DataSeries\DataSeries;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DataSeriesTest extends TestCase
{

    private Collection $periodWeatherData;
    private Collection $nationalEnergyData;
    private Collection $internationalEnergyData;

    /**
     * Test weather data
     * @dataProvider weatherDataProvider
     */
    public function test_weather_data(
        TimePeriod $period, 
        int $itemCount, 
        array $tempValue, 
        array $cloudValue, 
        array $windValue, 
        array $rainValue, 
        array $snowValue
    ) {
        $fields = ['temperature', 'wind', 'clouds', 'rain', 'snow'];
        $dataSeries = DataSeriesFactory::generate($this->periodWeatherData, $fields, $period)->getValues();

        foreach ($fields as $field) {
            $this->assertCount($itemCount, $dataSeries[$field]);
        }

        $this->assertEquals($tempValue[1], $dataSeries['temperature'][$tempValue[0]]['value']);
        $this->assertEquals($cloudValue[1], $dataSeries['clouds'][$cloudValue[0]]['value']);
        $this->assertEquals($windValue[1], $dataSeries['wind'][$windValue[0]]['value']);
        $this->assertEquals($rainValue[1], $dataSeries['rain'][$rainValue[0]]['value']);
        $this->assertEquals($snowValue[1], $dataSeries['snow'][$snowValue[0]]['value']);
    }


    public function weatherDataProvider(): array
    {
        return [
            [TimePeriodFactory::generate('2022-01-01', 'day'), 24, [0, 2.74], [4, 64.03], [1, 0.93], [2, 1.24], [3, 1.28]],
            [TimePeriodFactory::generate('2022-01-01', 'week'), 7, [6, 1.74], [5, 68.72], [6, 5.65], [5, 0.93], [6, 1.18]],
            [TimePeriodFactory::generate('2022-01-01', 'month'), 6, [0, 3.69], [1, 64.75], [5, 5.05], [0, 0.91], [1, 0.67]],
            [TimePeriodFactory::generate('2022-06-05', 'year'), 12, [0, 4.28], [0, 68.46], [1, 5.05], [2, 0.9], [3, 0.43]]
        ];
    }

    /**
     * Test weather data
     * @dataProvider nationalEnergyDataProvider
     */
    public function test_national_energy_data(
        TimePeriod $period, 
        int $itemCount, 
        array $netPosValue, 
        array $priceValue, 
        array $generationValue, 
        array $loadValue,
        array $loadForecastValue
    ) {
        $fields = ['net_position', 'price', 'total_generation', 'load', 'load_forecast'];
        $dataSeries = DataSeriesFactory::generate($this->nationalEnergyData, $fields, $period)->getValues();

        foreach ($fields as $field) {
            $this->assertCount($itemCount, $dataSeries[$field]);
        }

        $this->assertEquals($netPosValue[1], $dataSeries['net_position'][$netPosValue[0]]['value']);
        $this->assertEquals($priceValue[1], $dataSeries['price'][$priceValue[0]]['value']);
        $this->assertEquals($generationValue[1], $dataSeries['total_generation'][$generationValue[0]]['value']);
        $this->assertEquals($loadValue[1], $dataSeries['load'][$loadValue[0]]['value']);
        $this->assertEquals($loadForecastValue[1], $dataSeries['load_forecast'][$loadForecastValue[0]]['value']);
    }


    public function nationalEnergyDataProvider(): array
    {
        return [
            [TimePeriodFactory::generate('2022-05-01', 'day'), 20, [0, -4990], [1, 289], [0, 6098], [1, 14429], [0, 12000]],
            [TimePeriodFactory::generate('2022-05-12', 'week'), 7, [1, 3178], [1, 231.67], [1, 10565], [6, 9602.33], [6, 10600]],
            [TimePeriodFactory::generate('2022-08-04', 'month'), 5, [2, 803.67], [2, 267.67], [2, 10093.67], [2, 9290], [2, 8900]],
            [TimePeriodFactory::generate('2022-09-04', 'year'), 12, [7, 803.67], [7, 267.67], [7, 10093.67], [8, 9110], [8, 8915]],
        ];
    }

    /**
     * Test weather data
     * @dataProvider internationalEnergyDataProvider
     */
    public function test_international_energy_data(
        TimePeriod $period, 
        int $itemCount, 
        array $comFlowValue, 
        array $phyFlowValue, 
        array $ntcValue
    ) {
        $fields = ['commercial_flow', 'physical_flow', 'net_transfer_capacity'];
        $dataSeries = DataSeriesFactory::generate($this->internationalEnergyData, $fields, $period)->getValues();

        foreach ($fields as $field) {
            $this->assertCount($itemCount, $dataSeries[$field]);
        }

        $this->assertEquals($comFlowValue[1], $dataSeries['commercial_flow'][$comFlowValue[0]]['value']);
        $this->assertEquals($phyFlowValue[1], $dataSeries['physical_flow'][$phyFlowValue[0]]['value']);
        $this->assertEquals($ntcValue[1], $dataSeries['net_transfer_capacity'][$ntcValue[0]]['value']);
    }


    public function internationalEnergyDataProvider(): array
    {
        return [
            [TimePeriodFactory::generate('2022-07-01', 'day'), 9, [6, 183], [7, -2870], [8, 5986]],
            [TimePeriodFactory::generate('2022-06-27', 'week'), 7, [4, -112.56], [4, -84.78], [4, 5943.78]],
            [TimePeriodFactory::generate('2022-07-03', 'month'), 5, [0, -112.56], [0, -84.78], [0, 5943.78]],
            [TimePeriodFactory::generate('2022-06-27', 'year'), 12, [6, -112.56], [6, -84.78], [6, 5943.78]],
        ];
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


        $this->nationalEnergyData = collect([
            // 01.05.22
            (object)['datetime' => '2022-05-01 00:00', 'net_position' => -4990, 'price' => 175, 'total_generation' => 6098, 'load' => 11088, 'load_forecast' => 12000], 
            (object)['datetime' => '2022-05-01 01:00', 'net_position' => -4749, 'price' => 289, 'total_generation' => 9680, 'load' => 14429, 'load_forecast' => 14300], 
            // 02.05.22
            (object)['datetime' => '2022-05-02 00:00', 'net_position' => -1565, 'price' => 207, 'total_generation' => 7722, 'load' => 9287, 'load_forecast' => 12000], 
            (object)['datetime' => '2022-05-02 01:00', 'net_position' => -1440, 'price' => 296, 'total_generation' => 9295, 'load' => 10735, 'load_forecast' => 12000], 
            (object)['datetime' => '2022-05-02 02:00', 'net_position' => 3850, 'price' => 151, 'total_generation' => 10358, 'load' => 6508, 'load_forecast' => 7000], 
            // 10.05
            (object)['datetime' => '2022-05-10 00:00', 'net_position' => 5334, 'price' => 190, 'total_generation' => 14570, 'load' => 9236, 'load_forecast' => 12000], 
            (object)['datetime' => '2022-05-10 01:00', 'net_position' => 1205, 'price' => 285, 'total_generation' => 8869, 'load' => 7664, 'load_forecast' => 8500], 
            (object)['datetime' => '2022-05-10 02:00', 'net_position' => 2995, 'price' => 220, 'total_generation' => 8256, 'load' => 5261, 'load_forecast' => 4300], 
            // 15.05.22
            (object)['datetime' => '2022-05-15 00:00', 'net_position' => -2622, 'price' => 218, 'total_generation' => 10476, 'load' => 13098, 'load_forecast' => 12000], 
            (object)['datetime' => '2022-05-15 01:00', 'net_position' => 2315, 'price' => 211, 'total_generation' => 10353, 'load' => 8038, 'load_forecast' => 7800], 
            (object)['datetime' => '2022-05-15 02:00', 'net_position' => 3123, 'price' => 226, 'total_generation' => 10794, 'load' => 7671, 'load_forecast' => 12000], 
            // 01.06.22
            (object)['datetime' => '2022-06-01 00:00', 'net_position' => 2373, 'price' => 269, 'total_generation' => 8528, 'load' => 6155, 'load_forecast' => 7230], 
            (object)['datetime' => '2022-06-01 01:00', 'net_position' => 3738, 'price' => 279, 'total_generation' => 9480, 'load' => 5742, 'load_forecast' => 4800], 
            (object)['datetime' => '2022-06-01 02:00', 'net_position' => 2436, 'price' => 253, 'total_generation' => 11574, 'load' => 9138, 'load_forecast' => 8900], 
            (object)['datetime' => '2022-06-01 03:00', 'net_position' => -1654, 'price' => 190, 'total_generation' => 5455, 'load' => 7109, 'load_forecast' => 6900], 
            // 15.08.22
            (object)['datetime' => '2022-08-15 00:00', 'net_position' => 679, 'price' => 290, 'total_generation' => 9948, 'load' => 9269, 'load_forecast' => 9400], 
            (object)['datetime' => '2022-08-15 01:00', 'net_position' => 8432, 'price' => 256, 'total_generation' => 13707, 'load' => 5275, 'load_forecast' => 5300], 
            (object)['datetime' => '2022-08-15 02:00', 'net_position' => -6700, 'price' => 257, 'total_generation' => 6626, 'load' => 13326, 'load_forecast' => 12000], 
            // 30.09.22
            (object)['datetime' => '2022-09-30 00:00', 'net_position' => 3531, 'price' => 211, 'total_generation' => 12108, 'load' => 8577, 'load_forecast' => 8400], 
            (object)['datetime' => '2022-09-30 01:00', 'net_position' => 1643, 'price' => 164, 'total_generation' => 11286, 'load' => 9643, 'load_forecast' => 9430]
        ]);


        $this->internationalEnergyData = collect([
            // 01.07.22
            (object)['datetime' => '2022-07-01 00:00', 'commercial_flow' => -302, 'physical_flow' => -280, 'net_transfer_capacity' => 5930], 
            (object)['datetime' => '2022-07-01 01:00', 'commercial_flow' => 349, 'physical_flow' => 289, 'net_transfer_capacity' => 5921],
            (object)['datetime' => '2022-07-01 02:00', 'commercial_flow' => -922, 'physical_flow' => -850, 'net_transfer_capacity' => 5928], 
            (object)['datetime' => '2022-07-01 03:00', 'commercial_flow' => 2901, 'physical_flow' => 3020, 'net_transfer_capacity' => 5908],
            (object)['datetime' => '2022-07-01 04:00', 'commercial_flow' => -381, 'physical_flow' => -340, 'net_transfer_capacity' => 5961], 
            (object)['datetime' => '2022-07-01 05:00', 'commercial_flow' => 79, 'physical_flow' => 90, 'net_transfer_capacity' => 5987],
            (object)['datetime' => '2022-07-01 06:00', 'commercial_flow' => 183, 'physical_flow' => 174, 'net_transfer_capacity' => 5901],
            (object)['datetime' => '2022-07-01 07:00', 'commercial_flow' => -2913, 'physical_flow' => -2870, 'net_transfer_capacity' => 5972], 
            (object)['datetime' => '2022-07-01 08:00', 'commercial_flow' => -7, 'physical_flow' => 4, 'net_transfer_capacity' => 5986]
        ]);


    }

}