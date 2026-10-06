<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\PropertyHookRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(PropertyHookRector::class);

    $rectorConfig->phpVersion(PhpVersion::PHP_84);
};
