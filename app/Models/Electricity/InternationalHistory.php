<?php

namespace App\Models\Electricity;

use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalHistory extends Model
{
    use HasFactory;

    protected $table = 'electricity_history_international';


    public static function periodDataOfCountry(TimePeriod $timePeriod, array $countries): Collection
    {
        return self::select(['datetime', 'commercial_flow', 'physical_flow', 'net_transfer_capacity'])
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->where('start_country', $countries[0]->getCode())
            ->where('end_country', $countries[1]->getCode())
            ->get();
    }


}
