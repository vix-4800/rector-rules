<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Transform\ValueObject\FuncCallToMethodCall;
use Rector\ValueObject\PhpVersion;
use Vix\RectorRules\LegacyRector\FuncCallToMethodCallRector;
use Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\SomeTranslator;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_80);

    $rectorConfig
        ->ruleWithConfiguration(FuncCallToMethodCallRector::class, [
            new FuncCallToMethodCall('view', 'Namespaced\SomeRenderer', 'render'),

            new FuncCallToMethodCall('translate', SomeTranslator::class, 'translateMethod'),

            new FuncCallToMethodCall(
                'Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\some_view_function',
                'Namespaced\SomeRenderer',
                'render'
            ),
        ]);
};
