<?php

namespace App\Models\Electricity;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $table = "electricity_generation";


    public static function getPeriodData(TimePeriod $timePeriod, Country $country): Collection
    {
        $fields = ['datetime', 'value'];
        return self::select($fields)
            ->where('countr', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->get();
    }
}