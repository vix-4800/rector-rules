<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SkipDifferentVariable
{
    private string $name;

    private string $surname;

    public function getName(): string
    {
        return $this->surname;
    }

    public function setName(string $name): void
    {
        $this->surname = ucfirst($name);
    }
}
