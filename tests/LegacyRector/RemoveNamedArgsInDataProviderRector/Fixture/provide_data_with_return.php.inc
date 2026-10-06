<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithReturn extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        return ['namedArg' => 100, 'another' => 'arg1'];
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RemoveNamedArgsInDataProviderRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataWithReturn extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public static function provideData()
    {
        return [100, 'arg1'];
    }
}

?>
