<?php

namespace App\Http\Controllers;

use App\Models\AvailableCountry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AvailableCountriesController extends Controller
{
    
    public function __invoke(Request $req, ?string $code = null): JsonResponse
    {
        return response()->json($this->fetchResult($code));
    }


    private function fetchResult(?string $code): array
    {
        if (is_null($code)) {
            return [
                'countries' => AvailableCountry::select(['short_name', 'official_name', 'code'])
                    ->get()
            ];
        }
        return [
            'valid_country' => AvailableCountry::where('code', $code)->count() === 1
        ];
    }

}
