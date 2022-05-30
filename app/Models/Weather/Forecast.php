<?php

namespace App\Models\Weather;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    use HasFactory;

    protected $table = "weather_points_forecast";


    public static function periodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        return self::select(['datetime', 'temperature', 'wind', 'clouds', 'rain', 'snow', 'weather_stations.name AS station'])
            ->join('weather_stations', 'station_id', '=', 'weather_stations.id')
            ->where('weather_stations.country', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->get();
    }

}
