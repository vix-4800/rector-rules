<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedForeachIfsToEarlyContinueRector\Fixture;

class SkipSingleLine
{
    public function run()
    {
        $items = [];

        foreach ($values as $value) {
            if ($value === 5) {
                $items[] = 'maybe';
            }
        }
    }
}
