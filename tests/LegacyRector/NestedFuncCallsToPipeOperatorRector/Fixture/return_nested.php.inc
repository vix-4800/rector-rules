<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class ReturnNested
{
    public function run()
    {
        return trim(strtolower(htmlspecialchars('  Hello World!  ')));
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class ReturnNested
{
    public function run()
    {
        return '  Hello World!  '
            |> htmlspecialchars(...)
            |> strtolower(...)
            |> trim(...);
    }
}

?>
