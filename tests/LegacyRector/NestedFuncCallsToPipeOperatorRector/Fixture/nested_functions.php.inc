<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class NestedFunctions
{
    public function run()
    {
        $result = trim(strtolower(htmlspecialchars('  Hello World!  ')));
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class NestedFunctions
{
    public function run()
    {
        $result = '  Hello World!  '
            |> htmlspecialchars(...)
            |> strtolower(...)
            |> trim(...);
    }
}

?>
