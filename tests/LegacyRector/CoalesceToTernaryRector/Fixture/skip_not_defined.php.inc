<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipNotDefined
{
    public function run()
    {
        return $name ?? 'tom';
    }
}
