<?php

namespace App\Models;

use App\Entities\Country\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeanValue extends Model
{
    use HasFactory;


    public static function singleValue(string $name, Country $country): float
    {
        $result = self::select('value')
            ->where('name', $name)
            ->where('country', $country->getCode())
            ->get();
        if ($result && $result->count() === 1) {
            return floatval($result->first()->value);
        }
        return 0.0;
    }

}
