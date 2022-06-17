<?php

namespace App\Http\Controllers;

use App\Models\AvailableCountry;
use App\Models\Electricity\NationalHistory;
use App\Models\Weather\History;
use App\Models\Weather\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CurrentDataController extends Controller
{
    
    public function __invoke (Request $request): JsonResponse
    {
        $targetDatetime = new \DateTimeImmutable('-1 day');
        return response()->json([
            'datetime' => $targetDatetime->format('Y-m-d H:00'),
            'electricity' => $this->electricityData($targetDatetime),
            'weather' => $this->weatherData($targetDatetime) 
        ]);
    }


    private function electricityData (\DateTimeImmutable $datetime): Collection
    {
        return NationalHistory::select(['country', 'net_position', 'price', 'total_generation', 'load'])
            ->where('datetime', $datetime->format('Y-m-d H:00'))   
            ->groupBy('country')
            ->groupBy('datetime')
            ->get();
    }


    private function weatherData (\DateTimeImmutable $datetime): array
    {
        $result = [];
        foreach (AvailableCountry::get() as $country) {
            $weatherStationAmount = Station::where('country', $country->code)->count();
            $result[] = History::select('country')
                ->selectRaw("SUM(temperature)/$weatherStationAmount AS temperature")
                ->selectRaw("SUM(wind)/$weatherStationAmount AS wind")
                ->selectRaw("SUM(clouds)/$weatherStationAmount AS clouds")
                ->selectRaw("SUM(rain)/$weatherStationAmount AS rain")
                ->selectRaw("SUM(snow)/$weatherStationAmount AS snow")
                ->where('country', $country->code)
                ->where('datetime', $datetime->format('Y-m-d H:00'))
                ->groupBy('datetime')
                ->first();
        }
        return $result;
    }

}
