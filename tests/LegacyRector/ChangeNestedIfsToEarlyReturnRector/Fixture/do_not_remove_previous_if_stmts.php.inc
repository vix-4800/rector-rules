<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedIfsToEarlyReturnRector\Fixture;

class DoNotRemovePreviousIfStmts
{
	public function foo($condition1, $condition2)
    {
        $value = 1;

        if ($condition1) {
            if ($condition2) {
                return 0;
            }
        }

        return $value;
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedIfsToEarlyReturnRector\Fixture;

class DoNotRemovePreviousIfStmts
{
	public function foo($condition1, $condition2)
    {
        $value = 1;

        if (!$condition1) {
            return $value;
        }
        if ($condition2) {
            return 0;
        }

        return $value;
    }
}

?>
