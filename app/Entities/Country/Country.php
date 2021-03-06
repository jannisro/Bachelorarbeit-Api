<?php

namespace App\Entities\Country;

abstract class Country
{

    private string $code;
    private string $displayName;
    private int $id;

    public function getCode(): string
    {
        return $this->code;
    }

    protected function setCode(string $code): Country
    {
        $this->code = strtoupper($code);
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    protected function setDisplayName(string $name): Country
    {
        $this->displayName = $name;
        return $this;
    }

}