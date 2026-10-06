<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\SwitchNegatedTernaryRector;

return RectorConfig::configure()
    ->withRules([SwitchNegatedTernaryRector::class]);
