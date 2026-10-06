<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\NewInInitializerRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(NewInInitializerRector::class);

    $rectorConfig->phpVersion(PhpVersion::PHP_81);
};
