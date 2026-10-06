<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use stdClass;

class SkipDynamicNew
{
    private object $stdClass;

    public function __construct(
        string $fallbackClass,
        ?stdClass $stdClass = null
    ) {
        $this->stdClass = $stdClass ?? new $fallbackClass;
    }
}
