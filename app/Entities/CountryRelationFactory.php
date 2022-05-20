<?php

namespace App\Entities;

use App\Models\AvailableCountryRelation;

class CountryRelationFactory extends CountryRelation
{

    private function __construct() {}


    public static function generate(string $startCountryCode, string $endCountryCode): ?CountryRelation
    {
        $country1 = CountryFactory::generate($startCountryCode);
        $country2 = CountryFactory::generate($endCountryCode);
        if ($country1 && $country2 && self::validateRelation($country1, $country2)) {
            return (new self)
                ->setStartCountry($country1)
                ->setEndCountry($country2);
        }
        return null;
    }


    private static function validateRelation(Country $country1, Country $country2): bool
    {
        $relation = AvailableCountryRelation::where('country_start', $country1->getCode())
            ->where('country_end', $country2->getCode());
        return $relation && $relation->count() === 1;
    }
    
}