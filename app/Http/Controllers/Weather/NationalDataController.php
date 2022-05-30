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
        $dataSeries = DataSeriesFactory::generate(
            $this->getHistoryAndForecast($timePeriod, $country),
            ['temperature', 'wind', 'clouds', 'rain', 'snow'],
            $timePeriod
        );
        return [
            'temperature' => $dataSeries->getValues()['temperature'],
            'wind' => $dataSeries->getValues()['wind'],
            'clouds' => $dataSeries->getValues()['clouds'],
            'rain' => $dataSeries->getValues()['rain'],
            'snow' => $dataSeries->getValues()['snow']
        ];
    }


    private function getHistoryAndForecast(TimePeriod $timePeriod, Country $country): Collection
    {
        // Period is entirely in the past
        if ($timePeriod->getEnd()->getTimestamp() < time()) {
            return History::periodDataOfCountry($timePeriod, $country);
        }
        // Period is entirely in the future
        elseif ($timePeriod->getStart() > time()) {
            return Forecast::periodDataOfCountry($timePeriod, $country);
        }
        // Period intersects history and future
        else {
            $result = [];
            foreach (History::periodDataOfCountry($timePeriod, $country) as $historyRow) {
                $result[] = $historyRow;
            }
            foreach (Forecast::periodDataOfCountry($timePeriod, $country) as $forecastRow) {
                $result[] = $forecastRow;
            }
            return collect($result);
        }
    }

}
