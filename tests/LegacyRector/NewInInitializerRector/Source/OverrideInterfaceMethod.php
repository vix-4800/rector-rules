<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source;

use DateTime;

interface OverrideInterfaceMethod
{
    public function __construct(
        ?DateTime $dateTime = null
    );
}
