<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/16be33c1c2364d51038752a299fce01043d42576/rules-tests/Php84/Rector/Class_/DeprecatedAnnotationToDeprecatedAttributeRector/DeprecatedAnnotationToDeprecatedAttributeRectorTest.php
 */
#[CoversClass(DeprecatedAnnotationToDeprecatedAttributeRector::class)]
final class DeprecatedAnnotationToDeprecatedAttributeRectorTest extends AbstractRectorTestCase
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

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
