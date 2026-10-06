<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\DisallowedEmptyRuleFixerRector;

return RectorConfig::configure()
    ->withRules([DisallowedEmptyRuleFixerRector::class]);
