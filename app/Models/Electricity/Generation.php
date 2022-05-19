<?php

namespace App\Models\Electricity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $table = "electricity_generation";

    public function timeSeriesItem(array $params, array $timePeriod): array
    {
        return [];
    }
}