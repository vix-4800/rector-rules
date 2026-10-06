<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class TwoNestedFunctions
{
    public function run(array $input)
    {
        assert(is_string($input['email']));
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

final class TwoNestedFunctions
{
    public function run(array $input)
    {
        $input['email']
            |> is_string(...)
            |> assert(...);
    }
}

?>
