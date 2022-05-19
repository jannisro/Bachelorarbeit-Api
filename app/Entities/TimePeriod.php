<?php

namespace App\Entities;

abstract class TimePeriod
{

    private array $steps = [];
    private \DateTimeImmutable $start;
    private \DateTimeImmutable $end;
    private string $name;
    private string $displayName;

    public function getSteps(): array
    {
        return $this->steps;
    }

    protected function addStep(array $step): TimePeriod
    {
        $this->steps[] = $step;
        return $this;
    }

    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    protected function setStart(\DateTimeImmutable $start): TimePeriod
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): \DateTimeImmutable
    {
        return $this->end;
    }

    protected function setEnd(\DateTimeImmutable $end): TimePeriod
    {
        $this->end = $end;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function setName(string $name): TimePeriod
    {
        $this->name = $name;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    protected function setDisplayName(string $name): TimePeriod
    {
        $this->displayName = $name;
        return $this;
    }

}