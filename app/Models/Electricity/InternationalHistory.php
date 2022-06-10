<?php

namespace App\Models\Electricity;

use App\Entities\Country\Country;
use App\Entities\TimePeriod\TimePeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InternationalHistory extends Model
{
    use HasFactory;

    protected $table = 'electricity_history_international';


    public static function periodDataOfCountryRelation(TimePeriod $timePeriod, array $countries): Collection
    {
        return self::select(['datetime', 'commercial_flow', 'physical_flow', 'net_transfer_capacity'])
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->where('start_country', $countries[0]->getCode())
            ->where('end_country', $countries[1]->getCode())
            ->get();
    }


    public static function summedPeriodDataOfCountry(TimePeriod $timePeriod, Country $country): Collection
    {
        $outgoing = self::select('datetime')
            ->selectRaw('SUM(physical_flow) AS physical_flow')
            ->selectRaw('SUM(commercial_flow) AS commercial_flow')
            ->selectRaw('SUM(net_transfer_capacity) AS net_transfer_capacity')
            ->where('start_country', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->groupBy('datetime')
            ->get()
            ->toArray();
        $incoming = self::select('datetime')
            ->selectRaw('SUM(physical_flow) AS physical_flow')
            ->selectRaw('SUM(commercial_flow) AS commercial_flow')
            ->selectRaw('SUM(net_transfer_capacity) AS net_transfer_capacity')
            ->where('end_country', $country->getCode())
            ->where('datetime', '>=', $timePeriod->getStart()->format('Y-m-d H:i'))
            ->where('datetime', '<', $timePeriod->getEnd()->format('Y-m-d H:i'))
            ->groupBy('datetime')
            ->get();
        $result = [];
        foreach ($incoming as $index => $incomingItem) {
            $outgoingPhysicalFlow = isset($outgoing[$index]) ? (float) $outgoing[$index]['physical_flow'] : 0;
            $outgoingCommercialFlow = isset($outgoing[$index]) ? (float) $outgoing[$index]['commercial_flow'] : 0;
            $outgoingNtc = isset($outgoing[$index]) ? (float) $outgoing[$index]['net_transfer_capacity'] : 0;
            $result[] = (object)[
                'datetime' => $incomingItem->datetime,
                'physical_flow' => (float) $incomingItem->physical_flow - $outgoingPhysicalFlow,
                'commercial_flow' => (float) $incomingItem->commercial_flow - $outgoingCommercialFlow,
                'net_transfer_capacity' => (float) $incomingItem->net_transfer_capacity - $outgoingNtc,
            ];
        }
        return collect($result);
    }

}
