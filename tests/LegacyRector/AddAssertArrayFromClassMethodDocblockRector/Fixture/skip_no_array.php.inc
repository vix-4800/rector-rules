<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class SkipNoArray
{
    /**
     * @param int[] $items
     */
    public function run($items)
    {
    }
}
