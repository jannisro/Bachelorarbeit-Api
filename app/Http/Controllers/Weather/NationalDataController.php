<?php

namespace App\Http\Controllers\Weather;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\Weather\Forecast;
use App\Models\Weather\History;
use App\Models\Weather\Station;
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
            'data' => $this->getDataOutput($timePeriod, $country)
        ]);
    }


    private function getDataOutput(TimePeriod $timePeriod, Country $country): array
    {
        $overallDataSeries = DataSeriesFactory::generate(
            $this->historyOrForecast($timePeriod, 'periodDataOfCountry', [$timePeriod, $country]), 
            ['temperature', 'wind', 'clouds', 'rain', 'snow'],
            $timePeriod
        );
        return [
            'stations' => $this->weatherPerStation($timePeriod, $country),
            'overall' => [
                'temperature' => $overallDataSeries->getValues()['temperature'],
                'wind' => $overallDataSeries->getValues()['wind'],
                'clouds' => $overallDataSeries->getValues()['clouds'],
                'rain' => $overallDataSeries->getValues()['rain'],
                'snow' => $overallDataSeries->getValues()['snow']
            ]
        ];
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
        elseif ($timePeriod->getStart() > time()) {
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
