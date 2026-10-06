<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\RemoveNamedArgsInDataProviderRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RemoveNamedArgsInDataProviderRector::class);
};
