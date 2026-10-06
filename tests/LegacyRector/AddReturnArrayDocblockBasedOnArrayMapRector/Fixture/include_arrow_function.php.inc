<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

final class IncludeArrowFunction
{
    public function run(array $items)
    {
        return array_map(fn ($item): int => 1000, $items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnArrayDocblockBasedOnArrayMapRector\Fixture;

final class IncludeArrowFunction
{
    /**
     * @return int[]
     */
    public function run(array $items)
    {
        return array_map(fn ($item): int => 1000, $items);
    }
}

?>
