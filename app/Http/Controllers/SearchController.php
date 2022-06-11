<?php

namespace App\Http\Controllers;

use App\Models\Electricity\InternationalHistory;
use App\Models\Electricity\NationalHistory;
use App\Models\Weather\History;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    
    public function __invoke (Request $req): JsonResponse
    {
        $nationalFields = ['country', 'total_generation', 'load', 'load_forecast', 'price', 'net_position'];
        $nationalBuilder = NationalHistory::select(['country'])->selectRaw('DATE(`datetime`) AS `date`');

        $internationalFields = ['country_start', 'country_end', 'commercial_flow', 'physical_flow', 'net_transfer_capacity'];
        $internationalBuilder = InternationalHistory::selectRaw('`start_country` AS `country`')->selectRaw('DATE(`datetime`) AS `date`');

        $weatherFields = ['country', 'temperature', 'clouds', 'wind', 'rain', 'snow'];
        $weatherBuilder = History::select(['country'])->selectRaw('DATE(`datetime`) AS `date`');

        return $this->outputUnifiedResults([
            $this->queryResult($nationalBuilder, $nationalFields, $req),
            $this->queryResult($internationalBuilder, $internationalFields, $req),
            $this->queryResult($weatherBuilder, $weatherFields, $req),
        ]);
        
    }


    private function outputUnifiedResults (array $segmentedResults): JsonResponse 
    {
        $result = $segmentedResults[0] ? $segmentedResults[0]->toArray() : [];
        foreach ($segmentedResults as $resultSegment) {
            $result = array_merge($result, $this->findMissingEntries($result, $resultSegment));
        }
        return response()->json([
            'results' => $result
        ]);
    }


    private function queryResult (Builder $builder, array $availableAttributes, Request $req): Collection
    {
        $result = $this->modelWithPeriodRange($builder, $req);
        $result = $this->modelWithExistingAttributes($result, $availableAttributes, $req); 
        return $result->limit(80)
            ->groupBy('country')
            ->groupBy(DB::raw('DATE(`datetime`)'))
            ->get();
    }


    private function modelWithPeriodRange (Builder $builder, Request $req): Builder
    {
        return $req->has(['period_start', 'period_end'])
            ? $builder->where([ ['datetime', '>=', $req->period_start], ['datetime', '<=', $req->period_end] ])
            : $builder;
    }


    private function modelWithExistingAttributes (Builder $builder, $availableAttributes, Request $req): Builder
    {
        $result = $builder;
        foreach ($availableAttributes as $attr) {
            if ($req->has(["{$attr}_start", "{$attr}_end"])) {
                $from = "{$attr}_start";
                $to = "{$attr}_end";
                $result = $builder->where([ [$attr, '>=', $req->$from], [$attr, '<=', $req->$to] ]);
            }
        }
        return $result;
    }


    private function findMissingEntries (array $existingEntries, Collection $entriesToCheck): array
    {
        $result = [];
        foreach ($entriesToCheck as $newRow) {
            $rowExists = false;
            foreach ($existingEntries as $existingRow) {
                if ($existingRow['date'] === $newRow->date && $existingRow['country'] === $newRow->country) {
                    $rowExists = true;
                    break;
                }
            }
            if (!$rowExists) {
                $result[] = [
                    'date' => $newRow->date,
                    'country' => $newRow->country
                ];
            }
        }
        return $result;
    }

}
