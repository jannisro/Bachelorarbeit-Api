<?php

namespace App\Http\Controllers\Electricity;

use App\Http\Controllers\Controller;
use App\Models\Electricity\Generation;
use App\Models\Electricity\InstalledCapacity;
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
        if ($this->validateInput($country, $timePeriod, $date)) {
            return response()->json([
                'country' => $this->countryName,
                'time_period' => $this->timePeriodName,
                'data' => [
                    'generation' => $this->timePeriodData(Generation::class),
                    'installedCapacity' => InstalledCapacity::timePeriodData($this->getCountry(), $this->getTimePeriod())
                ]
            ]);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function timePeriodData(string $model): array
    {
        return call_user_func(
            [$model, 'timePeriodData'], 
            $this->countryCode, 
            $this->timePeriod
        );
    }

}
