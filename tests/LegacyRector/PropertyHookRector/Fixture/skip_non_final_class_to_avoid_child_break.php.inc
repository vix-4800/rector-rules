<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

class SkipNonFinalClassToAvoidChildBreak
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = ucfirst($name);
    }
}
