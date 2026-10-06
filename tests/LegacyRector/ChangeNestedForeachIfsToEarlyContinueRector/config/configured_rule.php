<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\ChangeNestedForeachIfsToEarlyContinueRector;

return RectorConfig::configure()
    ->withRules([ChangeNestedForeachIfsToEarlyContinueRector::class]);
