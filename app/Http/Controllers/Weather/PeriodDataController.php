<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeriodDataController extends Controller
{
    
    public function __invoke(Request $req): JsonResponse
    {
        return response()->json([]);
    }

}
