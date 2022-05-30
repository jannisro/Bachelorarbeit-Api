<?php

namespace App\Http\Controllers\Electricity;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\Electricity\Generation;
use App\Models\Electricity\InstalledCapacity;
use App\Models\Electricity\NationalHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
            NationalHistory::periodDataOfCountry($timePeriod, $country),
            ['net_position', 'price', 'total_generation', 'load', 'load_forecast'],
            $timePeriod
        );
        $result = [
            'total_generation' => $dataSeries->getValues()['total_generation'],
            'load' => $dataSeries->getValues()['load'],
            'load_forecast' => $dataSeries->getValues()['load_forecast'],
            'net_position' => $dataSeries->getValues()['net_position'],
            'price' => $dataSeries->getValues()['price'],
            'installed_capacities' => InstalledCapacity::periodDataOfCountry($timePeriod, $country)
        ];
        if ($timePeriod->getName() === 'day') {
            $result['generation'] = Generation::periodDataOfCountry($timePeriod, $country);
        }
        return $result;
    }

}
