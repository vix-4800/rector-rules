<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/16be33c1c2364d51038752a299fce01043d42576/rules-tests/TypeDeclaration/Rector/ClassMethod/AddReturnArrayDocblockBasedOnArrayMapRector/AddReturnArrayDocblockBasedOnArrayMapRectorTest.php
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
#[CoversClass(AddReturnArrayDocblockBasedOnArrayMapRector::class)]
final class AddReturnArrayDocblockBasedOnArrayMapRectorTest extends AbstractRectorTestCase
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
