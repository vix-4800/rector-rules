<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\SomeValueObject;

class SkipPassClassConstFetchArgDynamicClass
{
    private DateTime $dateTime;

    public function __construct(
        ?DateTime $dateTime = null
    ) {
        $class = SomeValueObject::class;
        $this->dateTime = $dateTime ?? new DateTime($class::NOW);
    }
}
