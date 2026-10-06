<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\RenameDeprecatedMethodCallRector;

return RectorConfig::configure()
    ->withRules([RenameDeprecatedMethodCallRector::class]);
