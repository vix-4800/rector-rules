<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeDataProvider extends TestCase
{
    #[DataProvider('provideValues')]
    public function testValue(int $value, string $label): void
    {
        self::assertGreaterThan(0, $value);
    }

    public static function provideValues(): iterable
    {
        yield 'first case' => ['value' => 100, 'label' => 'first'];
        yield 'second case' => ['value' => 200, 'label' => 'second'];
    }
}
-----
<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeDataProvider extends TestCase
{
    #[DataProvider('provideValues')]
    public function testValue(int $value, string $label): void
    {
        self::assertGreaterThan(0, $value);
    }

    public static function provideValues(): iterable
    {
        yield 'first case' => [100, 'first'];
        yield 'second case' => [200, 'second'];
    }
}
