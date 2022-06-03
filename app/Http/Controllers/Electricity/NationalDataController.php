<?php

namespace App\Http\Controllers\Electricity;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Entities\DataSeries\DataSeriesFactory;
use App\Entities\TimePeriod\TimePeriod;
use App\Entities\TimePeriod\TimePeriodFactory;
use App\Http\Controllers\Controller;
use App\Models\Electricity\Generation;
use App\Models\Electricity\InstalledCapacity;
use App\Models\Electricity\NationalHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class NationalDataController extends Controller
{
    
    
    public function __invoke(
        Request $req, 
        string $countryCode, 
        string $timePeriodName, 
        string $date
    ): JsonResponse
    {
        $timePeriod = TimePeriodFactory::generate($date, $timePeriodName);
        $country = CountryFactory::generate($countryCode);
        if (!is_null($timePeriod) && !is_null($country)) {
            return $this->getResponse($timePeriod, $country);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function getResponse(TimePeriod $timePeriod, Country $country): JsonResponse
    {
        return response()->json([
            'country' => $country->getDisplayName(),
            'time_period' => $timePeriod->getDisplayName(),
            'data' => $this->getDataOutput($timePeriod, $country)
        ]);
    }


    private function getDataOutput(TimePeriod $timePeriod, Country $country): array
    {
        $dataSeries = DataSeriesFactory::generate(
            NationalHistory::periodDataOfCountry($timePeriod, $country),
            ['net_position', 'price', 'total_generation', 'load', 'load_forecast'],
            $timePeriod
        );
        $result = [
            'total_generation' => $dataSeries->getValues()['total_generation'],
            'load' => $dataSeries->getValues()['load'],
            'load_forecast' => $dataSeries->getValues()['load_forecast'],
            'net_position' => $dataSeries->getValues()['net_position'],
            'price' => $dataSeries->getValues()['price'],
            'installed_capacities' => InstalledCapacity::periodDataOfCountry($timePeriod, $country)
        ];
        if ($timePeriod->getName() === 'day') {
            $result['generation'] = $this->groupGenerationByPsrType(
                $this->getGenerationOfDay($timePeriod->getStart(), $country)
            );
        }
        return $result;
    }

    private function getGenerationOfDay(\DateTimeImmutable $date, Country $country): Collection
    {
        return Generation::select(['datetime', 'psr_type', 'value', 'psr_types.name'])
            ->join('psr_types', 'psr_type', '=', 'psr_types.code')
            ->where('country', $country->getCode())
            ->where('datetime', 'LIKE', "{$date->format('Y-m-d')} %")
            ->orderBy('datetime', 'ASC')
            ->get();
    }


    private function groupGenerationByPsrType(Collection $generationOfDay): array
    {
        $result = [];
        foreach ($generationOfDay as $generationItem) {
            if (isset($result[$generationItem->psr_type])) {
                $result[$generationItem->psr_type]['hourly'][] = [
                    'datetime' => $generationItem->datetime,
                    'value' => $generationItem->value
                ];
            }
            else {
                $result[$generationItem->psr_type] = [
                    'name' => $generationItem->name,
                    'hourly' => [
                        'datetime' => $generationItem->datetime,
                        'value' => $generationItem->value
                    ]
                ];
            }
        }
        return $result;
    }

}
