<?php

namespace App\Models\Weather;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaylightHoursHistory extends Model
{
    use HasFactory;

    protected $table = "weather_daylight_hours_history";
}
