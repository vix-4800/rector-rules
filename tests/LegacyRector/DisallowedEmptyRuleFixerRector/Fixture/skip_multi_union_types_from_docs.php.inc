<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class SkipMultiUnionTypesFromDoc
{
    /**
     * @param int|int[]|null|string $id
     */
    public function get($id)
    {
        return empty($id);
    }
}
