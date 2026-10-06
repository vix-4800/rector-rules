<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\ConfiguredMockEntityToSetterObjectRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-phpunit/tree/e68085d656eeec87d524f1be0f13212c16fca68b/rules-tests/CodeQuality/Rector/Expression/ConfiguredMockEntityToSetterObjectRector/ConfiguredMockEntityToSetterObjectRectorTest.php
 */
#[CoversClass(ConfiguredMockEntityToSetterObjectRector::class)]
final class ConfiguredMockEntityToSetterObjectRectorTest extends AbstractRectorTestCase
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
