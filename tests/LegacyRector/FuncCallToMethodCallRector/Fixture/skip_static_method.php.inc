<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use function Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\some_view_function;

final class SkipStaticMethod
{
    public static function run()
    {
        $result = \translate('name');
    }
}
