<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipPossiblyUndefined
{
    public function run()
    {
        if (rand(0, 1)) {
            $name = 'tom';
        }

        return $name ?? 'tom';
    }
}
