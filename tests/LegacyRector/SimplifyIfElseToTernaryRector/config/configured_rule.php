<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\SimplifyIfElseToTernaryRector;

return RectorConfig::configure()
    ->withRules([SimplifyIfElseToTernaryRector::class]);
