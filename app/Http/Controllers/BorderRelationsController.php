<?php

namespace App\Http\Controllers;

use App\Models\AvailableCountry;
use App\Models\AvailableCountryRelation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BorderRelationsController extends Controller
{
    
    public function __invoke(Request $req, string $code): JsonResponse
    {
        return response()->json([
            'borders' => $this->fetchBorders($code)
        ]);
    }


    private function fetchBorders(string $code): array
    {
        $borders = AvailableCountryRelation::select('country_end', 'short_name')
            ->join('available_countries', 'country_end', '=', 'code')
            ->where('country_start', $code)
            ->get();
        if ($borders->count() > 0) {
            return $this->resultToArray($borders);
        }
        return ['c' => $borders->count()];
    }


    private function resultToArray(Collection $borderRelations): array
    {
        $result = [];
        foreach ($borderRelations as $borderRelation) {
            $result[] = [
                'name' => $borderRelation->short_name,
                'code' => $borderRelation->country_end
            ];
        }
        return $result;
    }

}
