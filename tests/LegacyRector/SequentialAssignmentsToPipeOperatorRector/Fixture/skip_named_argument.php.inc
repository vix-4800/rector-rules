<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function namedArgument(string $input): string
{
    $first = someFunction(second: $input);
    $result = strtoupper($first);

    return $result;
}
