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


    public static function periodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        return self::select(['datetime as dt', 'value', 'psr_types.name AS psr_type'])
            ->join('psr_types', 'psr_type', '=', 'psr_types.code')
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->where('country', $country->getCode())
            ->get();
    }
}