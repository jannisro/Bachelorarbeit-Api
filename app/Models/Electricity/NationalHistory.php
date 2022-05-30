<?php

namespace App\Models\Electricity;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalHistory extends Model
{
    use HasFactory;

    protected $table = 'electricity_history_national';


    public static function periodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        return self::select(['datetime', 'net_position', 'price', 'total_generation', 'load', 'load_forecast'])
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->where('country', $country->getCode())
            ->get();
    }


}
