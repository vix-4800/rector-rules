<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

use stdClass;

final class SkipAlreadyHasReturnDoc
{
    /**
     * @return list<stdClass>
     */
    public function process(array $items)
    {
        return array_map(function ($item): stdClass {
            return $item;
        }, $items);
    }
}
