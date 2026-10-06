<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector;

use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\NestedFuncCallsToPipeOperatorRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/16be33c1c2364d51038752a299fce01043d42576/rules-tests/Php85/Rector/Expression/NestedFuncCallsToPipeOperatorRector/NestedFuncCallsToPipeOperatorRectorTest.php
 */
#[CoversClass(NestedFuncCallsToPipeOperatorRector::class)]
final class NestedFuncCallsToPipeOperatorRectorTest extends AbstractRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @param array<mixed> $configuration
     */
    #[DataProvider('provideInvalidConfiguration')]
    #[Test]
    public function rejectsInvalidConfiguration(array $configuration): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->make(NestedFuncCallsToPipeOperatorRector::class)->configure($configuration);
    }

    /**
     * @return iterable<string, array{array<mixed>}>
     */
    public static function provideInvalidConfiguration(): iterable
    {
        yield 'below minimum depth' => [[NestedFuncCallsToPipeOperatorRector::MINIMUM_DEPTH => 1]];
        yield 'string depth' => [[NestedFuncCallsToPipeOperatorRector::MINIMUM_DEPTH => '3']];
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
