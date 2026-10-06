<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddAssertArrayFromClassMethodDocblockRector;
use Vix\RectorRules\LegacyRector\Enum\AssertClassName;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddAssertArrayFromClassMethodDocblockRector::class, [
        AssertClassName::BEBERLEI,
    ]);
};
