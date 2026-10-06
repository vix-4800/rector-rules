<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Vix\RectorRules\LegacyRector\AddInterfaceByTraitRector;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\AnotherTrait;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeInterface;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\TopMostInterface;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(AddInterfaceByTraitRector::class, [
            SomeTrait::class => SomeInterface::class,
            AnotherTrait::class => TopMostInterface::class,
        ]);
};
