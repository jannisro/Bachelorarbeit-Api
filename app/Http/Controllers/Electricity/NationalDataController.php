<?php

namespace App\Http\Controllers\Electricity;

use App\Http\Controllers\Controller;
use App\Models\Electricity\Generation;
use App\Models\Electricity\InstalledCapacity;
use App\Services\DataSeriesService;
use App\Services\TimePeriodService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NationalDataController extends Controller
{
    
    
    public function __invoke(
        Request $req, 
        string $country, 
        string $timePeriod, 
        string $date
    ): JsonResponse
    {
        $countryObject = $this->validateCountry($country);
        $period = $this->validateTimePeriod($timePeriod, $date);
        if (!is_null($countryObject) && !is_null($period)) {
            return $this->getResponse($countryObject, $period);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function getResponse(/**/): JsonResponse
    {
        return response()->json([
            'country' => '',
            'time_period' => '',
            'data' => [
                'generation' => '',
                'installedCapacity' => ''
            ]
        ]);
    }

}
