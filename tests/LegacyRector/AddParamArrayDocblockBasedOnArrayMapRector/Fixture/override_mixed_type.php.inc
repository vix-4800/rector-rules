<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddParamArrayDocblockBasedOnArrayMapRector\Fixture;

final class OverrideMixedType
{
    /**
     * @param mixed[] $items
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

final class OverrideMixedType
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
