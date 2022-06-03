<?php

namespace App\Http\Controllers\Electricity;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use App\Http\Controllers\Controller;
use App\Models\Electricity\Generation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class GenerationPerTypeController extends Controller
{
    
    
    public function __invoke(
        Request $req, 
        string $countryCode, 
        string $date
    ): JsonResponse
    {
        $country = CountryFactory::generate($countryCode);
        $dateTime = new \DateTimeImmutable($date);
        if (!is_null($country) && $dateTime) {
            return $this->getResponse($dateTime, $country);
        }
        return $this->outputError(400, "Invalid parameters passed");
    }


    private function getResponse(\DateTimeImmutable $date, Country $country): JsonResponse
    {
        return response()->json([
            'psr_types' => $this->groupGenerationByPsrType(
                $this->getGenerationOfDay($date, $country)
            )
        ]);
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
