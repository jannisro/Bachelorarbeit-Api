<?php

namespace App\Models\Weather;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaylightHoursForecast extends Model
{
    use HasFactory;

    protected $table = "weather_daylight_hours_forecast";
}
