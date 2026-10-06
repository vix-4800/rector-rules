<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SkipReadonlyPromotedProperty
{
    public function __construct(
        private readonly int $value,
    )
    {
    }

    public function getValue()
    {
        return $this->value;
    }
}
