<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipProvideDataWithNoNamedArgs extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        yield [100];
    }
}

?>
