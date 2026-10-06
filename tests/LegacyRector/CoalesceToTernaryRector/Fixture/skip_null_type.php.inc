<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipNullType
{
    public function run(null $name)
    {
        return $name ?? 'tom';
    }
}
