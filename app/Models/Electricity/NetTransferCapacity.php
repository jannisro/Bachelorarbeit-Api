<?php

namespace App\Models\Electricity;

use App\Entities\Country\CountryRelation;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetTransferCapacity extends Model
{
    use HasFactory;

    protected $table = "electricity_net_transfer_capacity";


    public static function getPeriodData(TimePeriod $timePeriod, CountryRelation $countryRelation): Collection
    {
        $fields = ['datetime', 'value'];
        return self::select($fields)
            ->where('country_start', $countryRelation->getStartCountry()->getCode())
            ->where('country_end', $countryRelation->getEndCountry()->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->get();
    }
}
