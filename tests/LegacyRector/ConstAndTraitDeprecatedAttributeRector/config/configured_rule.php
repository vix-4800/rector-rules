<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ConstAndTraitDeprecatedAttributeRector::class);
    $rectorConfig->phpVersion(PhpVersion::PHP_85);
};
