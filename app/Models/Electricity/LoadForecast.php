<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadForecast extends Model
{
    use HasFactory;

    protected $table = "electricity_load_forecast";
}
