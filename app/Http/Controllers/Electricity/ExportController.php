<?php

namespace App\Http\Controllers\Electricity;

use App\Models\Electricity\InternationalHistory;
use App\Models\Electricity\NationalHistory;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;
use App\Services\CsvBuildService;

class ExportController extends Controller
{
    
    public function __invoke(string $areaName, string $countryCode, string $date): StreamedResponse
    {
        $timePeriod = ($dt = date_create_immutable($date))
            ? [$dt->format('Y-m-01'), $dt->format('Y-m-t')]
            : [date('Y-m-01'), date('Y-m-t')];

        $datarows = $this->datarows($areaName, $countryCode, $timePeriod);
        $fieldNames = $this->getFieldNames($areaName);
        
        return response()->streamDownload(
            function () use ($fieldNames, $datarows) {
                echo CsvBuildService::build($datarows, $fieldNames);
            }, 
            "electricity_{$areaName}_{$countryCode}_{$timePeriod[0]}_{$timePeriod[1]}.csv"
        );
    }


    private function datarows(string $areaName, string $countryCode, array $timePeriod): Collection
    {
        if ($areaName === 'international') {
            return InternationalHistory::select($this->getFieldNames($areaName))
                ->where('start_country', $countryCode)
                ->orWhere('end_country', $countryCode)
                ->where('datetime', '>=', $timePeriod[0])
                ->where('datetime', '<', $timePeriod[1])
                ->get();
        }
        return NationalHistory::select($this->getFieldNames($areaName, true))
            ->where('country', $countryCode)
            ->where('datetime', '>=', $timePeriod[0])
            ->where('datetime', '<', $timePeriod[1])
            ->get();
    }


    private function getFieldNames(string $areaName): array
    {
        if ($areaName === 'international') {
            return [
                'start_country', 'end_country', 'datetime', 
                'commercial_flow', 'physical_flow', 'net_transfer_capacity'
            ];
        }
        return [
            'country', 'datetime', 'net_position', 
            'price', 'total_generation', 'load', 'load_forecast'
        ];
    }

}
