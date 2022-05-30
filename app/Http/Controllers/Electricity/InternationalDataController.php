<?php

namespace App\Http\Controllers\Electricity;

use App\Entities\Country\CountryFactory;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\Electricity\InternationalHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InternationalDataController extends Controller
{
    
    
    public function __invoke(
        Request $req, 
        string $countryCode1, 
        string $countryCode2, 
        string $timePeriodName, 
        string $date
    ): JsonResponse
    {
        $timePeriod = TimePeriodFactory::generate($date, $timePeriodName);
        $country1 = CountryFactory::generate($countryCode1);
        $country2 = CountryFactory::generate($countryCode2);
        if (!is_null($timePeriod) && !is_null($country1) && !is_null($country2)) {
            return $this->getResponse($timePeriod, [$country1, $country2]);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function getResponse(TimePeriod $timePeriod, array $countries): JsonResponse
    {
        return response()->json([
            'start_country' => $countries[0]->getDisplayName(),
            'end_country' => $countries[1]->getDisplayName(),
            'time_period' => $timePeriod->getDisplayName(),
            'data' => $this->getDataOutput($timePeriod, $countries)
        ]);
    }


    private function getDataOutput(TimePeriod $timePeriod, array $countries): array
    {
        $dataSeries = DataSeriesFactory::generate(
            InternationalHistory::periodDataOfCountry($timePeriod, $countries),
            ['commercial_flow', 'physical_flow', 'net_transfer_capacity'],
            $timePeriod
        );
        return [
            'commercial_flow' => $dataSeries->getValues()['commercial_flow'],
            'physical_flow' => $dataSeries->getValues()['physical_flow'],
            'net_transfer_capacity' => $dataSeries->getValues()['net_transfer_capacity']
        ];
    }

}
