<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class SkipMoreStatements
{
    public function run()
    {
        if ($cond1) {
            if ($cond2) {
                return 'foo';
            }
            return 'bar';
        }
    }
}

?>
