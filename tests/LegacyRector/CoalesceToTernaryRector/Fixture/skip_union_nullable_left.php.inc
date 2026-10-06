<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipUnionNullableLeft
{
    public function run(null|string $name)
    {
        return $name ?? 'tom';
    }
}
