<?php

namespace App\Entities;

use App\Models\AvailableCountry;

class CountryFactory extends Country
{

    private function __construct() {}


    public static function generate(array $codes): ?array
    {
        if (count($codes) === 1 || count($codes) === 2) {
            return self::validateCountries($codes);
        }
        return null;
    }


    private static function validateCountries($codes): ?array
    {
        $result = [];
        foreach ($codes as $code) {
            if (preg_match('/\b\D\D\b/i', $code) && $country = self::prepareCountry($code)) {
                $result[] = $country;
            }
            else {
                return null;
            }
        }
        return $result;
    }


    private static function prepareCountry(string $code): ?Country
    {
        if ($name = self::getCountryName($code)) {
            return (new self)
                ->setDisplayName($name)
                ->setCode($code);
        }
        return null;
    }


    private static function getCountryName(string $code): ?string
    {
        $country = AvailableCountry::select('official_name')->where('code', strtoupper($code));
        if ($country && $country->count() === 1) {
            return $country->first()->official_name;
        }
        return null;
    }
    
}