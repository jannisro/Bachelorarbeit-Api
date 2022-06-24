<?php

namespace App\Http\Controllers;

use App\Models\AvailableCountry;
use App\Models\Electricity\NationalHistory;
use App\Models\Weather\History;
use Illuminate\Http\JsonResponse;

class CurrentDataController extends Controller
{
    
    public function __invoke (): JsonResponse
    {
        $targetDatetime = new \DateTimeImmutable('-1 day');
        $resultWithElectricity = $this->electricityData($targetDatetime);
        return response()->json([
            'datetime' => $targetDatetime->format('Y-m-d H:00'),
            'data' => $this->addWeatherData($targetDatetime, $resultWithElectricity)
        ]);
    }


    private function electricityData (\DateTimeImmutable $datetime): array
    {
        $result = [];
        $data = NationalHistory::select(['country', 'net_position', 'price', 'total_generation', 'load'])
            ->where('datetime', $datetime->format('Y-m-d H:00'))   
            ->groupBy('country')
            ->groupBy('datetime')
            ->get();
        foreach ($data as $row) {
            $result[$row->country] = [
                'net_position' => $row->net_position,
                'price' => $row->price,
                'generation' => $row->total_generation,
                'load' => $row->load,
            ];
        }
        return $result;
    }


    private function addWeatherData (\DateTimeImmutable $datetime, array $existingResult): array
    {
        $result = $existingResult;
        foreach (AvailableCountry::get() as $country) {
            $data = History::select('country')
                ->selectRaw("AVG(temperature) AS temperature")
                ->selectRaw("AVG(wind) AS wind")
                ->selectRaw("AVG(clouds) AS clouds")
                ->where('country', $country->code)
                ->where('datetime', $datetime->format('Y-m-d H:00'))
                ->groupBy('datetime')
                ->first();
            $result[$data->country]['wind'] = $data->wind;
            $result[$data->country]['clouds'] = $data->clouds;
            $result[$data->country]['temperature'] = $data->temperature;
        }
        return $result;
    }

}
