<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class SkipOuterSpreadNestedCall
{
    public function flatten(array $groups): array
    {
        return array_merge(...array_values($groups));
    }
}
