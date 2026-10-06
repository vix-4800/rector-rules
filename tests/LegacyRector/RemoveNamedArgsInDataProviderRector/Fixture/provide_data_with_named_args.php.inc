<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithNamedArgs extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        yield ['namedArg' => 100, 'another' => 'arg1'];
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithNamedArgs extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        yield [100, 'arg1'];
    }
}

?>
