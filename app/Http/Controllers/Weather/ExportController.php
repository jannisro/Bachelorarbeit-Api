<?php

namespace App\Http\Controllers\Weather;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;
use App\Models\Weather\History;
use App\Services\CsvBuildService;

class ExportController extends Controller
{

    const FIELD_NAMES = [
        'station_name', 'lat', 'lng', 'country', 'datetime', 
        'temperature', 'clouds', 'wind', 'rain', 'snow'
    ];
    const FIELD_NAMES_DB = [
        'name as station_name', 'lat', 'lng', 'weather_points_history.country', 
        'datetime', 'temperature', 'clouds', 'wind', 'rain', 'snow'
    ];

    
    public function __invoke(string $countryCode, string $date): StreamedResponse
    {
        $timePeriod = ($dt = date_create_immutable($date))
            ? [$dt->format('Y-m-01'), $dt->format('Y-m-t')]
            : [date('Y-m-01'), date('Y-m-t')];
        $datarows = $this->datarows($countryCode, $timePeriod);
        
        return response()->streamDownload(
            function () use ($datarows) {
                echo CsvBuildService::build($datarows, self::FIELD_NAMES);
            }, 
            "weather_{$countryCode}_{$timePeriod[0]}_{$timePeriod[1]}.csv"
        );
    }


    private function datarows(string $countryCode, array $timePeriod): Collection
    {
        return History::select(self::FIELD_NAMES_DB)
            ->join('weather_stations', 'station_id', '=', 'weather_stations.id')
            ->where('weather_points_history.country', $countryCode)
            ->where('datetime', '>=', $timePeriod[0])
            ->where('datetime', '<', $timePeriod[1])
            ->get();
    }

}
