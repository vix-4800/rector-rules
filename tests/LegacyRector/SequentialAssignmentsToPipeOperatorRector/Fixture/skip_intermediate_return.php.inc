<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function intermediateIsReturned(string $input): array
{
    $first = trim($input);
    $result = strtoupper($first);

    return [$first, $result];
}
