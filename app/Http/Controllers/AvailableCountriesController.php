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
        return response()->json([
            'countries' => $this->fetchCountries($code)
        ]);
    }


    private function fetchCountries(?string $code): Collection
    {
        $countries = AvailableCountry::select(['short_name', 'official_name', 'code']);
        if (!is_null($code)) {
            $countries->where('code', $code);
        }
        return $countries->get();
    }

}
