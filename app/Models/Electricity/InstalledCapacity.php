<?php

namespace App\Models\Electricity;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstalledCapacity extends Model
{
    use HasFactory;

    protected $table = "electricity_installed_capacities";


    public static function periodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        return self::select(['value', 'psr_types.name as psr_type'])
            ->join('psr_types', 'psr_type', '=', 'psr_types.code')
            ->where('country', $country->getCode())
            ->where('year', $timePeriod->getStart()->format('Y'))
            ->get();
    }
}
