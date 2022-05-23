<?php

namespace Tests\Unit\Entities;

use App\Entities\Country\CountryRelationFactory;
use Tests\TestCase;

class CountryRelationTest extends TestCase
{

    public function test_invalid_codes()
    {
        $this->assertNull(CountryRelationFactory::generate('UR', 'TM'));
        $this->assertNull(CountryRelationFactory::generate('TZ', 'DE'));
        $this->assertNull(CountryRelationFactory::generate('ES', 'FRM'));
    }

    public function test_invalid_relations()
    {
        $this->assertNull(CountryRelationFactory::generate('DE', 'PT'));
        $this->assertNull(CountryRelationFactory::generate('FI', 'AL'));
        $this->assertNull(CountryRelationFactory::generate('PL', 'CH'));
        $this->assertNull(CountryRelationFactory::generate('RO', 'CZ'));
    }

    public function test_valid_relations()
    {
        $this->runCountryRelationsAssertions('DE', 'AT', 'Germany', 'Austria');
        $this->runCountryRelationsAssertions('PT', 'ES', 'Portugal', 'Spain');
        $this->runCountryRelationsAssertions('FI', 'SE', 'Finland', 'Sweden');
        $this->runCountryRelationsAssertions('ME', 'AL', 'Montenegro', 'Albania');
        $this->runCountryRelationsAssertions('IT', 'CH', 'Italy', 'Switzerland');
    }

    private function runCountryRelationsAssertions(string $code1, string $code2, string $name1, string $name2): void
    {
        $relation = CountryRelationFactory::generate($code1, $code2);
        $this->assertEquals($code1, $relation->getStartCountry()->getCode());
        $this->assertEquals($code2, $relation->getEndCountry()->getCode());
        $this->assertEquals($name1, $relation->getStartCountry()->getDisplayName());
        $this->assertEquals($name2, $relation->getEndCountry()->getDisplayName());
    }

}