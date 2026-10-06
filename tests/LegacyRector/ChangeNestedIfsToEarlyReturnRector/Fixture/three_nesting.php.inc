<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedIfsToEarlyReturnRector\Fixture;

class ThreeNesting
{
    public function run($value)
    {
        if ($value === 5) {
            if ($value === 15) {
                if ($value === 30) {
                    return 'yes';
                }
            }
        }

        return 'no';
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedIfsToEarlyReturnRector\Fixture;

class ThreeNesting
{
    public function run($value)
    {
        if ($value !== 5) {
            return 'no';
        }
        if ($value !== 15) {
            return 'no';
        }
        if ($value === 30) {
            return 'yes';
        }

        return 'no';
    }
}

?>
