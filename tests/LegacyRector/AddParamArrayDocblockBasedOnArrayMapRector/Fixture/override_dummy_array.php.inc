<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddParamArrayDocblockBasedOnArrayMapRector\Fixture;

final class OverrideDummyArray
{
    /**
     * @param array $items
     */
    public function run(array $items): void
    {
        array_map(fn (string $item) => trim($item), $items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddParamArrayDocblockBasedOnArrayMapRector\Fixture;

final class OverrideDummyArray
{
    /**
     * @param string[] $items
     */
    public function run(array $items): void
    {
        array_map(fn (string $item) => trim($item), $items);
    }
}

?>
