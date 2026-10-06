<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source;

use DateTime;

abstract class OverrideAbstractMethod
{
    abstract public function __construct(
        ?DateTime $dateTime = null
    );
}
