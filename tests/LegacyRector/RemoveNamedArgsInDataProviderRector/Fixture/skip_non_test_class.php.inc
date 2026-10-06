<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;

final class NotATest
{
    #[DataProvider('provideValues')]
    public function run(int $value): void
    {
    }

    public static function provideValues(): iterable
    {
        yield ['value' => 100];
    }
}
