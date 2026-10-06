<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source;

final class InstantiableViaNamedConstructor
{
    public static function make(int $value): InstantiableViaNamedConstructor
    {
        return new InstantiableViaNamedConstructor($value);
    }

    public function __construct(public int $value)
    {
    }
}
