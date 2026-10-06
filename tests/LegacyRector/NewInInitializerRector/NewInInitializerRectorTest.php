<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\NewInInitializerRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/dfb760f8b4df920d881992cda5727e30930dd71a/rules-tests/Php81/Rector/ClassMethod/NewInInitializerRector/NewInInitializerRectorTest.php
 */
#[CoversClass(NewInInitializerRector::class)]
final class NewInInitializerRectorTest extends AbstractRectorTestCase
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
