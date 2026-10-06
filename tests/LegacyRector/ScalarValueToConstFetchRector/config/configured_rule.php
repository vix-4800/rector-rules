<?php

declare(strict_types=1);

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Config\RectorConfig;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Vix\RectorRules\LegacyRector\ScalarValueToConstFetchRector;
use Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Source\ClassWithConst;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig
        ->ruleWithConfiguration(
            ScalarValueToConstFetchRector::class,
            [
                new ScalarValueToConstFetch(
                    new Int_(10),
                    new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_INT'))
                ),
                new ScalarValueToConstFetch(
                    new Float_(10.1),
                    new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_FLOAT'))
                ),
                new ScalarValueToConstFetch(
                    new String_('ABC'),
                    new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_STRING'))
                ),
            ]
        );
};
