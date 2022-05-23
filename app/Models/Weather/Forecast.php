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


    public static function getPeriodData(TimePeriod $timePeriod, Country $country): Collection
    {
        $fields = ['datetime', 'temperature', 'wind', 'clouds', 'rain', 'snow', 'st.country as country'];
        return self::select($fields)
            ->join('weather_stations st', 'station_id', '=', 'st.id')
            ->where('country', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->get();
    }

}
