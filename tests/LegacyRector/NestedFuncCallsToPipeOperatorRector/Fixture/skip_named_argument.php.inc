<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

function namedNestedArgument(string $input): string
{
    return someFunction(second: trim($input));
}
