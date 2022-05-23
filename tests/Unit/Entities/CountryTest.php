<?php

namespace Tests\Unit\Entities;

use App\Entities\Country\Country;
use App\Entities\Country\CountryFactory;
use Tests\TestCase;

class CountryTest extends TestCase
{

    public function test_invalid_code_format()
    {
        $this->assertNull(CountryFactory::generate('xml'));
        $this->assertNull(CountryFactory::generate('d'));
        $this->assertNull(CountryFactory::generate('12'));
        $this->assertNull(CountryFactory::generate('de1'));
        $this->assertNull(CountryFactory::generate('fooo'));
    }

    public function test_invalid_codes() 
    {
        $this->assertNull(CountryFactory::generate('US'));
        $this->assertNull(CountryFactory::generate('BR'));
        $this->assertNull(CountryFactory::generate('GB'));
    }

    public function test_valid_single_country()
    {
        $de = CountryFactory::generate('DE');
        $this->testCountryProperties($de, 'Germany', 'DE');

        $fr = CountryFactory::generate('FR');
        $this->testCountryProperties($fr, 'France', 'FR');

        $se = CountryFactory::generate('SE');
        $this->testCountryProperties($se, 'Sweden', 'SE');

        $ro = CountryFactory::generate('RO');
        $this->testCountryProperties($ro, 'Romania', 'RO');

        $cz = CountryFactory::generate('CZ');
        $this->testCountryProperties($cz, 'Czech Republic', 'CZ');
    }

    public function test_valid_single_countries_lowercase_code()
    {
        $fi = CountryFactory::generate('fi');
        $this->testCountryProperties($fi, 'Finland', 'FI');

        $al = CountryFactory::generate('al');
        $this->testCountryProperties($al, 'Albania', 'AL');
    }

    private function testCountryProperties(Country $country, string $expectedName, string $expectedCode): void
    {
        $this->assertEquals($expectedName, $country->getDisplayName());
        $this->assertEquals($expectedCode, $country->getCode());
    }

}