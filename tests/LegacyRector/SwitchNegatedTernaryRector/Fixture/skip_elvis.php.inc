<?php

namespace Vix\RectorRules\Tests\LegacyRector\SwitchNegatedTernaryRector\Fixture;

class SkipElvis
{
    public function run(bool $upper, string $name)
    {
        return ! $upper ?: strtoupper($name);
    }
}

?>
