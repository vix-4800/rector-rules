<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddParamArrayDocblockBasedOnArrayMapRector;

return RectorConfig::configure()
    ->withRules([AddParamArrayDocblockBasedOnArrayMapRector::class]);
