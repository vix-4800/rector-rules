<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function spreadArgument(array $values): string
{
    $first = implode(...$values);
    $result = strtoupper($first);

    return $result;
}
