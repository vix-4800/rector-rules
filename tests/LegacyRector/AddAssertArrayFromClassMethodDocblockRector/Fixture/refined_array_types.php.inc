<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class RefinedArrayTypes
{
    /**
     * @param non-empty-array<non-empty-string, positive-int> $items
     */
    public function run(array $items): void
    {
        \Webmozart\Assert\Assert::allInteger($items);
    }
}

-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddAssertArrayFromClassMethodDocblockRector\Fixture;

final class RefinedArrayTypes
{
    /**
     * @param non-empty-array<non-empty-string, positive-int> $items
     */
    public function run(array $items): void
    {
        \Webmozart\Assert\Assert::allString(array_keys($items));
        \Webmozart\Assert\Assert::allInteger($items);
    }
}

