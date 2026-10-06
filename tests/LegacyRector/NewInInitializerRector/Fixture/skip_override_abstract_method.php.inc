<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\OverrideAbstractMethod;

class SkipOverrideAbstractMethod extends OverrideAbstractMethod
{
    private DateTime $dateTime;

    public function __construct(
        ?DateTime $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? new DateTime('now');
    }
}
