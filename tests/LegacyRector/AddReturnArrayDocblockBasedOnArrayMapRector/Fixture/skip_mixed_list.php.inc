<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

final class SkipMixedList
{
    /**
     * @return list<mixed>
     */
    public function process(array $items): array
    {
        return array_map(static fn ($item): int => $item, $items);
    }
}
