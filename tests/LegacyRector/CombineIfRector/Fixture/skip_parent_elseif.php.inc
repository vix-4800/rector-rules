<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class SkipParentElseif
{
    public function run()
    {
        if ($cond1) {
            if ($cond2) {
                return 'foo';
            }
        } elseif($cond3) {
            return 'bar';
        }
    }
}
