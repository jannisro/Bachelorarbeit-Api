<?php

namespace App\Http\Controllers\Weather;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\Weather\Forecast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NationalDataController extends Controller
{
    
    public function __invoke(Request $req, string $countryCode, string $timePeriodName, string $date): JsonResponse
    {
        $timePeriod = TimePeriodFactory::generate($date, $timePeriodName);
        $country = CountryFactory::generate($countryCode);
        if ($timePeriod && $country) {
            return $this->getValidResponse($timePeriod, $country);
        }
        return $this->outputError(400, 'Invalid parameters passed');
    }


    private function getValidResponse(TimePeriod $timePeriod, Country $country): JsonResponse
    {
        return response()->json([
            'country' => $country->getDisplayName(),
            'time_period' => $timePeriod->getDisplayName(),
            'historic_data' => [],
            'forecasted_data' => Forecast::getDataSeries($timePeriod, $country)
        ]);
    }

}
