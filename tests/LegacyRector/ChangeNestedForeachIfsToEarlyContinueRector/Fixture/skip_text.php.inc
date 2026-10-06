<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedForeachIfsToEarlyContinueRector\Fixture;

class SkipText
{
    public function run()
    {
        $items = [];

        foreach ($values as $value) {
            if ($value === 5) {
                if ($value2 === 10) {
                    $items[] = 'maybe';
                    return;
                }
            }
        }
    }
}
