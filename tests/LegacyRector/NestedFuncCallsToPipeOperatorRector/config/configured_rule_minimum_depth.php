<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\NestedFuncCallsToPipeOperatorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(NestedFuncCallsToPipeOperatorRector::class, [
        NestedFuncCallsToPipeOperatorRector::MINIMUM_DEPTH => 3,
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_85);
};
