<?php

namespace App\Entities;

use App\Models\AvailableCountry;

class CountryFactory extends Country
{

    private function __construct() {}


    public static function generate(string $code): ?Country
    {
        if (preg_match('/\b\D\D\b/i', $code) && $country = self::getCountry($code)) {
            return (new self)
                ->setDisplayName($country->official_name)
                ->setCode($country->code);
        }
        return null;
    }


    private static function getCountry(string $code): ?AvailableCountry
    {
        $country = AvailableCountry::select(['official_name', 'code'])
            ->where('code', strtoupper($code));
        if ($country && $country->count() === 1) {
            return $country->first();
        }
        return null;
    }
    
}