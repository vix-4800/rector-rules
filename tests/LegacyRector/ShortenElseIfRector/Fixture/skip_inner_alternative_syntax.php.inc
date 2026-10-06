<?php

namespace Vix\RectorRules\Tests\LegacyRector\ShortenElseIfRector\Fixture;

function skipInnerAlternativeSyntax($cond1, $cond2)
{
    if ($cond1) {
        $a = 1;
    } else {
        if ($cond2) :
            $a = 2;
        endif;
    }

    return $a;
}
