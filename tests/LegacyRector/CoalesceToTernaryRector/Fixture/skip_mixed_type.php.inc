<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipMixedType
{
    public function run(mixed $name)
    {
        return $name ?? 'tom';
    }
}
