<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class SkipDocblockBased
{
    /**
     * @param string $name
     */
    public function run($name)
    {
        return $name ?? 'tom';
    }
}
