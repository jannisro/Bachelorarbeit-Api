<?php

namespace App\Http\Controllers\Weather;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\MeanValue;
use App\Models\Weather\Forecast;
use App\Models\Weather\History;
use App\Models\Weather\Station;
use App\Services\DeviationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class NationalDataController extends Controller
{
    
    
    public function __invoke(
        Request $req, 
        string $countryCode, 
        string $timePeriodName, 
        string $date
    ): JsonResponse
    {
        $timePeriod = TimePeriodFactory::generate($date, $timePeriodName);
        $country = CountryFactory::generate($countryCode);
        if (!is_null($timePeriod) && !is_null($country)) {
            return $this->getResponse($timePeriod, $country);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function getResponse(TimePeriod $timePeriod, Country $country): JsonResponse
    {
        return response()->json([
            'country' => $country->getDisplayName(),
            'time_period' => $timePeriod->getDisplayName(),
            'previous_step' => $timePeriod->getPreviousStepDate()->format('Y-m-d'),
            'next_step' =>  $timePeriod->getNextStepDate()->format('Y-m-d'),
            'data' => $this->getDataOutput($timePeriod, $country)
        ]);
    }


    private function getDataOutput(TimePeriod $timePeriod, Country $country): array
    {
        $deviations = $this->overallAverageDeviations($country, $timePeriod);
        return [
            'stations' => $this->weatherPerStation($timePeriod, $country),
            'overall' => [
                'temperature' => $deviations['temperature'],
                'wind' => $deviations['wind'],
                'clouds' => $deviations['clouds'],
                'rain' => $deviations['rain'],
                'snow' => $deviations['snow']
            ]
        ];
    }


    private function overallAverageDeviations(Country $country, TimePeriod $timePeriod): array
    {
        $fields = ['temperature', 'wind', 'clouds', 'rain', 'snow'];
        $datarows = DataSeriesFactory::generate(
            $this->historyOrForecast($timePeriod, 'periodDataOfCountry', [$timePeriod, $country]), 
            $fields,
            $timePeriod
        )->getValues();

        $periodAvg = DeviationService::longTermAverage(
            $timePeriod, 
            History::where('country', $country->getCode()), 
            $fields
        );

        $result = [];
        array_map(function ($field) { $result[$field] = []; }, $fields);

        if (!is_null($periodAvg)) {
            for ($i = 0; $i < count($datarows['temperature']); $i++) {
                foreach ($fields as $field) {
                    $result[$field][] = [
                        'dt' => $datarows[$field][$i]['dt'],
                        'value' => $this->deviation($datarows[$field][$i]['value'], $periodAvg[$field])
                    ]; 
                }
            }
        }

        return $result;
    }


    private function deviation (int|float $currentValue, int|float $longTermAverage): int|float
    {
        if ($longTermAverage == 0 && $currentValue == 0) {
            return 0;
        }
        else if ($longTermAverage == 0) {
            return round((($currentValue / 0.01) - 1) * 100, 2);
        }
        return round((($currentValue / $longTermAverage) - 1) * 100, 2);
    }


    function weatherPerStation(TimePeriod $timePeriod, Country $country): array 
    {
        $result = [];
        foreach (Station::where('country', $country->getCode())->get() as $station) {
            $stationDataSeries = DataSeriesFactory::generate(
                $this->historyOrForecast($timePeriod, 'periodDataOfStation', [$timePeriod, $station]),
                ['temperature', 'wind', 'clouds', 'rain', 'snow'],
                $timePeriod
            );
            $result[] = [
                'name' => $station->name,
                'latLng' => [$station->lat, $station->lng],
                'temperature' => $stationDataSeries->getValues()['temperature'],
                'wind' => $stationDataSeries->getValues()['wind'],
                'clouds' => $stationDataSeries->getValues()['clouds'],
                'rain' => $stationDataSeries->getValues()['rain'],
                'snow' => $stationDataSeries->getValues()['snow'],
            ];
        }
        return $result;
    }


    private function historyOrForecast(TimePeriod $timePeriod, string $modelMethodName, array $modelMethodArgs): Collection
    {
        // Period is entirely in the past
        if ($timePeriod->getEnd()->getTimestamp() < time()) {
            return call_user_func_array([History::class, $modelMethodName], $modelMethodArgs);
        }
        // Period is entirely in the future
        elseif ($timePeriod->getStart()->getTimestamp() > time()) {
            return call_user_func_array([Forecast::class, $modelMethodName], $modelMethodArgs);
        }
        // Period intersects history and future
        else {
            $result = [];
            foreach (call_user_func_array([History::class, $modelMethodName], $modelMethodArgs) as $historyRow) {
                $result[] = $historyRow;
            }
            foreach (call_user_func_array([Forecast::class, $modelMethodName], $modelMethodArgs) as $forecastRow) {
                $result[] = $forecastRow;
            }
            return collect($result);
        }
    }

}
