<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\OverrideInterfaceMethod;

class SkipOverrideInterfaceMethod implements OverrideInterfaceMethod
{
    private DateTime $dateTime;

    public function __construct(
        ?DateTime $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? new DateTime('now');
    }
}
