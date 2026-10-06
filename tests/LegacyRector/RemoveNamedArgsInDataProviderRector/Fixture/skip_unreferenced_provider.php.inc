<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class UnreferencedProvider extends TestCase
{
    public static function provideValues(): iterable
    {
        yield ['value' => 100];
    }
}
