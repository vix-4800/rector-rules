<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector;

return RectorConfig::configure()
    ->withRules([AddReturnDocblockForDimFetchArrayFromAssignsRector::class]);
