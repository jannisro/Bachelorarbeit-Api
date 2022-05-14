<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use App\Models\Weather\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationController extends Controller
{
    
    public function __invoke(Request $req, ?string $country = null): JsonResponse
    {
        $stations = Station::select([
            'id', 'lat', 'lng', 'name', 'country'
        ]);
        if (is_null($country)) {
            return response()->json([
                'stations' =>$stations->get()
            ]);
        }
        return $stations->where('country', 'LIKE', "%$country%")->get();
    }

}
