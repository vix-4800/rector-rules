<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final readonly class SkipReadonlyClass
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
