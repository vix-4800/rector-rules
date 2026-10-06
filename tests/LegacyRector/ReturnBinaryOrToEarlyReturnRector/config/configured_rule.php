<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\ReturnBinaryOrToEarlyReturnRector;

return RectorConfig::configure()
    ->withRules([ReturnBinaryOrToEarlyReturnRector::class]);
