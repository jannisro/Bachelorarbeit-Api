<?php

namespace App\Http\Controllers\Electricity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PeriodDataController extends Controller
{
    
    public function __invoke(
        Request $req, 
        string $country, 
        string $timePeriod, 
        string $date
    ): JsonResponse
    {
        return response()->json(['message' => 'Hallo Welt']);
    }

}
