<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class SkipNegatedUnionTypeFromDoc
{
    /**
     * @param int|int[]|string $value
     */
    public function run($value)
    {
        return ! empty($value);
    }
}
