<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function adjacentChains(string $input, string $other): array
{
    $first = trim($input);
    $result = strtoupper($first);
    $second = trim($other);
    $otherResult = strtolower($second);

    return [$result, $otherResult];
}

-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function adjacentChains(string $input, string $other): array
{
    $result = $input
        |> trim(...)
        |> strtoupper(...);
    $otherResult = $other
        |> trim(...)
        |> strtolower(...);
    return [$result, $otherResult];
}

