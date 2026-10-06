<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\FixtureMinimumDepth;

final class MinimumDepth
{
    public function run(array $input)
    {
        $normalizedEmail = trim(strtolower(htmlspecialchars($input['email'])));
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\FixtureMinimumDepth;

final class MinimumDepth
{
    public function run(array $input)
    {
        $normalizedEmail = $input['email']
            |> htmlspecialchars(...)
            |> strtolower(...)
            |> trim(...);
    }
}

?>
