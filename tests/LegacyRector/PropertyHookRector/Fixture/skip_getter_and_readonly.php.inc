<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SkipGetterAndReadonly
{
    private readonly string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
