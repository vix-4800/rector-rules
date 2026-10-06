<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class SkipAlreadySet
{
    /**
     * @param int[] $items
     */
    public function run(array $items)
    {
        \Webmozart\Assert\Assert::allInteger($items);
    }
}
