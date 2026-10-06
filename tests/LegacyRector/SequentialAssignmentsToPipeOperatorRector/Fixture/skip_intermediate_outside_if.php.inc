<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function intermediateOutsideIf(string $input, bool $enabled): array
{
    $first = null;
    $result = null;
    if ($enabled) {
        $first = trim($input);
        $result = strtoupper($first);
    }

    return [$first, $result];
}
