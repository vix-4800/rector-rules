<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\ChangeOrIfContinueToMultiContinueRector;

return RectorConfig::configure()
    ->withRules([ChangeOrIfContinueToMultiContinueRector::class]);
