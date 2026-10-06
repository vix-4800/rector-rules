<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\FluentSettersToStandaloneCallMethodRector;

return RectorConfig::configure()
    ->withRules([FluentSettersToStandaloneCallMethodRector::class]);
