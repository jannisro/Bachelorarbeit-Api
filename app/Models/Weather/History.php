<?php

namespace App\Models\Weather;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class History extends Model
{
    use HasFactory;

    protected $table = "weather_points_history";


    public static function periodDataOfStation(TimePeriod $timePeriod, Station $station): Collection
    {
        return self::select(['datetime', 'temperature', 'wind', 'clouds', 'rain', 'snow'])
            ->where('station_id', $station->id)
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->get();
    }


    public static function periodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        $weatherStationAmount = Station::where('country', $country->getCode())->count();
        return self::select('datetime')
            ->selectRaw("SUM(temperature)/$weatherStationAmount AS temperature")
            ->selectRaw("SUM(wind)/$weatherStationAmount AS wind")
            ->selectRaw("SUM(clouds)/$weatherStationAmount AS clouds")
            ->selectRaw("SUM(rain)/$weatherStationAmount AS rain")
            ->selectRaw("SUM(snow)/$weatherStationAmount AS snow")
            ->join('weather_stations', 'station_id', '=', 'weather_stations.id')
            ->where('weather_stations.country', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->groupBy('datetime')
            ->get();
    }

}
