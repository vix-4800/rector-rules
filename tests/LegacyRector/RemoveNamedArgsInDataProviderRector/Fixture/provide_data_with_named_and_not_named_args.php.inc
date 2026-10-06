<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithNamedAndNotNamedArgs extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        yield ['namedArg' => 100, null];
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithNamedAndNotNamedArgs extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        yield [100, null];
    }
}

?>
