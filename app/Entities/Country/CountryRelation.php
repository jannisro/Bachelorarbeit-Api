<?php

namespace App\Entities\Country;

abstract class CountryRelation
{

    private Country $startCountry;
    private Country $endCountry;

    public function getStartCountry(): Country
    {
        return $this->startCountry;
    }

    protected function setStartCountry(Country $country): CountryRelation
    {
        $this->startCountry = $country;
        return $this;
    }

    public function getEndCountry(): Country
    {
        return $this->endCountry;
    }

    protected function setEndCountry(Country $country): CountryRelation
    {
        $this->endCountry = $country;
        return $this;
    }

}