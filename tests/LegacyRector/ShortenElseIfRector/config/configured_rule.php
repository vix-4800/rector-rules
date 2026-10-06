<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\ShortenElseIfRector;

return RectorConfig::configure()
    ->withRules([ShortenElseIfRector::class]);
