<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

final class SkipNestedFunctions
{
    public function run()
    {
        $result = trim(strtoupper("Hello World"));
    }
}
