<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector;

return RectorConfig::configure()
    ->withRules([AddReturnArrayDocblockBasedOnArrayMapRector::class]);
