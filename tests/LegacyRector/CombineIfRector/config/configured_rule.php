<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\CombineIfRector;

return RectorConfig::configure()
    ->withRules([CombineIfRector::class]);
