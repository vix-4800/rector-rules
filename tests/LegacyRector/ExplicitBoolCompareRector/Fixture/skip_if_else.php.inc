<?php

namespace Vix\RectorRules\Tests\LegacyRector\ExplicitBoolCompareRector\Fixture;

use PhpParser\Node\Stmt\If_;

final class SkipIfElse
{
    public function run(If_ $if)
    {
        if ((bool) $if->elseifs) {
            return true;
        }

        return false;
    }
}
