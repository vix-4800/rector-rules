<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class DistinctArrayBranches
{
    public function values(bool $strings): array
    {
        $values = [];
        if ($strings) {
            $values['name'] = 'Ada';
        } else {
            $values[0] = 123;
        }

        return $values;
    }
}

-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class DistinctArrayBranches
{
    /**
     * @return (array<int, int> | array<string, string>)
     */
    public function values(bool $strings): array
    {
        $values = [];
        if ($strings) {
            $values['name'] = 'Ada';
        } else {
            $values[0] = 123;
        }

        return $values;
    }
}

