<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersionFeature;
use Vix\RectorRules\LegacyRector\AddSensitiveParameterAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersionFeature::SENSITIVE_PARAMETER_ATTRIBUTE);

    $rectorConfig->ruleWithConfiguration(AddSensitiveParameterAttributeRector::class, [
        AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => ['password'],
    ]);
};
