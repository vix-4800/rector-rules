<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

function propertyAssignment(object $object, string $input): string
{
    $object->first = trim($input);
    $result = strtoupper($object->first);

    return $result;
}
