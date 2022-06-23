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
use App\Models\Electricity\InternationalHistory;
use App\Models\Electricity\NationalHistory;
use App\Services\DeviationService;
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
    )//: JsonResponse
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
            'previous_step' => $timePeriod->getPreviousStepDate()->format('Y-m-d'),
            'next_step' =>  $timePeriod->getNextStepDate()->format('Y-m-d'),
            'data' => $this->getDataOutput($timePeriod, $country)
        ]);
    }


    private function getDataOutput(TimePeriod $timePeriod, Country $country): array
    {
        $nationalDataSeries = DataSeriesFactory::generate(
            NationalHistory::periodDataOfCountry($timePeriod, $country),
            ['net_position', 'price', 'total_generation', 'load', 'load_forecast'],
            $timePeriod
        );

        $internationalDataSeries = DataSeriesFactory::generate(
            InternationalHistory::summedPeriodDataOfCountry($timePeriod, $country),
            ['commercial_flow', 'physical_flow', 'net_transfer_capacity'],
            $timePeriod
        );

        $nationalDeviations = DeviationService::calculate(
            $timePeriod, 
            NationalHistory::where('country', $country->getCode()), 
            ['net_position', 'price', 'total_generation', 'load']
        );

        $internationalDeviations = DeviationService::calculate(
            $timePeriod, 
            InternationalHistory::where('start_country', $country->getCode()), 
            ['commercial_flow', 'physical_flow', 'net_transfer_capacity']
        );

        $result = [
            'total_generation' => $nationalDataSeries->getValues()['total_generation'],
            'load' => $nationalDataSeries->getValues()['load'],
            'load_forecast' => $nationalDataSeries->getValues()['load_forecast'],
            'net_position' => $nationalDataSeries->getValues()['net_position'],
            'price' => $nationalDataSeries->getValues()['price'],
            'installed_capacities' => InstalledCapacity::periodDataOfCountry($timePeriod, $country),
            'physical_flow' => $internationalDataSeries->getValues()['physical_flow'],
            'commercial_flow' => $internationalDataSeries->getValues()['commercial_flow'],
            'net_transfer_capacity' => $internationalDataSeries->getValues()['net_transfer_capacity'],
            'mean_values' => [
                [ 'name' => 'Generation', 'value' => $nationalDeviations['total_generation'] ],
                [ 'name' => 'Load', 'value' => $nationalDeviations['load'] ],
                [ 'name' => 'Net Position', 'value' => $nationalDeviations['net_position'] ],
                [ 'name' => 'Price', 'value' => $nationalDeviations['price'] ],
                [ 'name' => 'Total Commercial Flow', 'value' => $internationalDeviations['commercial_flow'] ],
                [ 'name' => 'Total Physical Flow', 'value' => $internationalDeviations['physical_flow'] ],
                [ 'name' => 'Total NTC', 'value' => $internationalDeviations['net_transfer_capacity'] ],
            ]
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
                        [
                            'datetime' => $generationItem->datetime,
                            'value' => $generationItem->value
                        ]
                    ]
                ];
            }
        }
        return $result;
    }

}
