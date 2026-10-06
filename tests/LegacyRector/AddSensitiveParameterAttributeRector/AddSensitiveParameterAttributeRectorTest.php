<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector;

use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Vix\RectorRules\LegacyRector\AddSensitiveParameterAttributeRector;

/**
 * @internal
 * @see https://github.com/rectorphp/rector-src/tree/16be33c1c2364d51038752a299fce01043d42576/rules-tests/Php82/Rector/Param/AddSensitiveParameterAttributeRector/AddSensitiveParameterAttributeRectorTest.php
 */
#[CoversClass(AddSensitiveParameterAttributeRector::class)]
final class AddSensitiveParameterAttributeRectorTest extends AbstractRectorTestCase
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
        $this->make(AddSensitiveParameterAttributeRector::class)->configure($configuration);
    }

    /**
     * @return iterable<string, array{array<mixed>}>
     */
    public static function provideInvalidConfiguration(): iterable
    {
        yield 'non-array parameter names' => [[AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => 'password']];
        yield 'non-string parameter name' => [[AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => ['password', 123]]];
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
