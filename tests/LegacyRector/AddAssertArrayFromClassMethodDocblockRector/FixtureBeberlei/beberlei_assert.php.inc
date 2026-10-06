<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\FixtureBeberlei;

final class FloatIntBool
{
    /**
     * @param int[] $items
     * @param float[] $numbers
     * @param bool[] $options
     */
    public function run(array $items, array $numbers, array $options)
    {

    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\FixtureBeberlei;

final class FloatIntBool
{
    /**
     * @param int[] $items
     * @param float[] $numbers
     * @param bool[] $options
     */
    public function run(array $items, array $numbers, array $options)
    {
        \Assert\Assertion::allInteger($items);
        \Assert\Assertion::allFloat($numbers);
        \Assert\Assertion::allBoolean($options);
    }
}

?>
