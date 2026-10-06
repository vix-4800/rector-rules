<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddParamArrayDocblockBasedOnArrayMapRector\Fixture;

final class OverrideBareMixed
{
    /**
     * @param mixed $items
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

final class OverrideBareMixed
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
