<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipNullableLeft
{
    public function run(?string $name)
    {
        return $name ?? 'tom';
    }
}
