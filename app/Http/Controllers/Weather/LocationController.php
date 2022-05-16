<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use App\Models\Weather\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    
    public function __invoke(Request $req, ?string $country = null): JsonResponse
    {
        return response()->json([
            'locations' => $this->fetchLocations($country)
        ]);
    }


    private function fetchLocations(?string $country): array
    {
        $stations = Station::select([
            'id', 'lat', 'lng', 'name', 'country'
        ]);
        if (!is_null($country)) {
            $stations->where('country', $country);
        }
        return $stations->get()->asArray();
    }

}
