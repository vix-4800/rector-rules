<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\CoalesceToTernaryRector;

return RectorConfig::configure()
    ->withRules([CoalesceToTernaryRector::class]);
