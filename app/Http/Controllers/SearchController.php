<?php

namespace App\Http\Controllers;

use App\Models\Electricity\NationalHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    
    public function __invoke (Request $req): JsonResponse
    {
        $fields = [
            'total_generation', 'load', 'load_forecast', 'price', 'net_position', 'commercial_flow', 
            'physical_flow', 'net_transfer_capacity', 'temperature', 'clouds', 'wind', 'rain', 'snow'
        ];

        $builder = NationalHistory::select('weather_points_history.country')
            ->selectRaw('DATE(weather_points_history.`datetime`) AS `date`');
        $builder = $this->modelWithJoins($builder);
        $builder = $this->modelWithPeriodRange($builder, $req);
        $builder = $this->modelWithCountry($builder, $req);
        $builder = $this->modelWithExistingAttributes($builder, $fields, $req);
        $builder = $builder->groupBy('weather_points_history.country')
            ->groupBy('date')
            ->limit(30);

        return response()->json([
            'results' => $builder->get()
        ]);
    }


    private function modelWithJoins (Builder $builder): Builder
    {
        return $builder->join('electricity_history_international', function ($join) {
            $join->on('electricity_history_international.datetime', '=', 'electricity_history_national.datetime');
            $join->on('electricity_history_international.start_country', '=', 'electricity_history_national.country');
        })
            ->join('weather_points_history', function ($join) {
                $join->on('weather_points_history.datetime', '=', 'electricity_history_national.datetime');
                $join->on('weather_points_history.country', '=', 'electricity_history_national.country');
            });
    }


    private function modelWithCountry (Builder $builder, Request $req): Builder
    {
        return $req->has('country')
            ? $builder->where('weather_points_history.country', $req->country)
            : $builder;
    }


    private function modelWithPeriodRange (Builder $builder, Request $req): Builder
    {
        return $req->has(['period_start', 'period_end'])
            ? $builder->where([ 
                ['weather_points_history.datetime', '>=', $req->period_start], 
                ['weather_points_history.datetime', '<=', $req->period_end] 
            ])
            : $builder;
    }


    private function modelWithExistingAttributes (Builder $builder, $availableAttributes, Request $req): Builder
    {
        $result = $builder;
        foreach ($availableAttributes as $attr) {
            if ($req->has(["{$attr}_start", "{$attr}_end"])) {
                $from = "{$attr}_start";
                $to = "{$attr}_end";
                $result = $builder->where([ [$attr, '>=', $req->$from], [$attr, '<', $req->$to] ]);
            }
        }
        return $result;
    }

}
