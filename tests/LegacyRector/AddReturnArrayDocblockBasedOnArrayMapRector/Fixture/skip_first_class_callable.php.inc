<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

final class SkipFirstClassCallable
{
    public function run(array $items)
    {
        return array_map(...);
    }
}
