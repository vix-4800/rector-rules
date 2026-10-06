<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class SkipUntypedProperty
{
    public $items;

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function isNotEmpty()
    {
        return ! empty($this->items);
    }
}
