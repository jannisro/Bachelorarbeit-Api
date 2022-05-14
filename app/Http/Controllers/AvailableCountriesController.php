<?php

namespace App\Http\Controllers;

use App\Models\AvailableCountry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailableCountriesController extends Controller
{
    
    public function __invoke(Request $req): JsonResponse
    {
        return response()->json([
            'countries' => AvailableCountry::select(['short_name', 'official_name', 'code'])
                ->get()
                ->asArray()
        ]);
    }

}
