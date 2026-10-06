<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\NestedFuncCallsToPipeOperatorRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/16be33c1c2364d51038752a299fce01043d42576/rules-tests/Php85/Rector/Expression/NestedFuncCallsToPipeOperatorRector/NestedFuncCallsToPipeOperatorRectorMinimumDepthTest.php
 */
#[CoversClass(NestedFuncCallsToPipeOperatorRector::class)]
final class NestedFuncCallsToPipeOperatorRectorMinimumDepthTest extends AbstractRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/FixtureMinimumDepth');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule_minimum_depth.php';
    }
}
