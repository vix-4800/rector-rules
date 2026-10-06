<?php

namespace Vix\RectorRules\Tests\LegacyRector\SwitchNegatedTernaryRector\Fixture;

class SkipTruthy
{
    public function run(bool $upper, string $name)
    {
        return $upper
            ? strtoupper($name)
            : $name;
    }
}

?>
