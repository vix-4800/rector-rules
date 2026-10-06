<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function compactIntermediate(string $input): array
{
    $first = trim($input);
    $result = strtoupper($first);

    return compact('first', 'result');
}
