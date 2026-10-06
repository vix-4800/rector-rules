<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

final class SkipNonEmptyMixedArray
{
    /**
     * @return non-empty-array<mixed>
     */
    public function process(array $items): array
    {
        return array_map(static fn ($item): int => $item, $items);
    }
}
