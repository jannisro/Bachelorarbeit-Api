<?php

namespace Tests\Unit\Entities;

use App\Entities\Country;
use App\Entities\CountryFactory;
use Tests\TestCase;

class CountryTest extends TestCase
{

    public function test_invalid_code_format()
    {
        $this->assertNull(CountryFactory::generate(['xml', 'urt']));
        $this->assertNull(CountryFactory::generate(['d', 'de1']));
        $this->assertNull(CountryFactory::generate(['12', 'm']));
        $this->assertNull(CountryFactory::generate(['2es']));
        $this->assertNull(CountryFactory::generate(['fooo']));
    }

    public function test_invalid_param_length()
    {
        $this->assertNull(CountryFactory::generate([]));
        $this->assertNull(CountryFactory::generate(['de', 'fr', 'es']));
    }

    public function test_invalid_codes() 
    {
        $this->assertNull(CountryFactory::generate(['US']));
        $this->assertNull(CountryFactory::generate(['BR', 'CH']));
        $this->assertNull(CountryFactory::generate(['DE', 'GB']));
    }

    public function test_valid_single_country()
    {
        $de = CountryFactory::generate(['DE']);
        $this->assertCount(1, $de);
        $this->testCountryProperties($de[0], 'Germany', 'DE');

        $fr = CountryFactory::generate(['FR']);
        $this->assertCount(1, $fr);
        $this->testCountryProperties($fr[0], 'France', 'FR');

        $se = CountryFactory::generate(['SE']);
        $this->assertCount(1, $se);
        $this->testCountryProperties($se[0], 'Sweden', 'SE');

        $ro = CountryFactory::generate(['RO']);
        $this->assertCount(1, $ro);
        $this->testCountryProperties($ro[0], 'Romania', 'RO');

        $cz = CountryFactory::generate(['CZ']);
        $this->assertCount(1, $cz);
        $this->testCountryProperties($cz[0], 'Czech Republic', 'CZ');
    }

    public function test_valid_country_relation()
    {
        $deAt = CountryFactory::generate(['DE', 'AT']);
        $this->assertCount(2, $deAt);
        $this->testCountryProperties($deAt[0], 'Germany', 'DE');
        $this->testCountryProperties($deAt[1], 'Austria', 'AT');

        $atDe = CountryFactory::generate(['AT', 'DE']);
        $this->assertCount(2, $atDe);
        $this->testCountryProperties($atDe[0], 'Austria', 'AT');
        $this->testCountryProperties($atDe[1], 'Germany', 'DE');

        $itCh = CountryFactory::generate(['IT', 'CH']);
        $this->assertCount(2, $itCh);
        $this->testCountryProperties($itCh[0], 'Italy', 'IT');
        $this->testCountryProperties($itCh[1], 'Switzerland', 'CH');
    }

    private function testCountryProperties(Country $country, string $expectedName, string $expectedCode): void
    {
        $this->assertEquals($expectedName, $country->getDisplayName());
        $this->assertEquals($expectedCode, $country->getCode());
    }

}