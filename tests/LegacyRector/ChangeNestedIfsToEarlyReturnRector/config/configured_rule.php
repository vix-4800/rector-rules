<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\ChangeNestedIfsToEarlyReturnRector;

return RectorConfig::configure()
    ->withRules([ChangeNestedIfsToEarlyReturnRector::class]);
