<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class EmptyAssignedArray
{
    public function values(): array
    {
        $values = [];

        return $values;
    }
}

-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class EmptyAssignedArray
{
    /**
     * @return array{}
     */
    public function values(): array
    {
        $values = [];

        return $values;
    }
}

