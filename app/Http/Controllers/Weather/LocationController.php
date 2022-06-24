<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use App\Models\Weather\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class LocationController extends Controller
{
    
    public function __invoke(?string $country = null): JsonResponse
    {
        return response()->json([
            'locations' => $this->fetchLocations($country)
        ]);
    }


    private function fetchLocations(?string $country): Collection
    {
        $stations = Station::select([
            'id', 'lat', 'lng', 'name', 'country'
        ]);
        if (!is_null($country)) {
            $stations->where('country', $country);
        }
        return $stations->get();
    }

}
