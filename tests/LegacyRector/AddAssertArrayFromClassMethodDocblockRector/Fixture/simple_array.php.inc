<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class SimpleArray
{
    /**
     * @param int[] $items
     */
    public function run(array $items)
    {
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class SimpleArray
{
    /**
     * @param int[] $items
     */
    public function run(array $items)
    {
        \Webmozart\Assert\Assert::allInteger($items);
    }
}

?>
