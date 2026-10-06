<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class WithNegationBinaryOpPreviousIf
{
    public function run($a, $b, $data, $field)
    {
        if (!($a && $b)) {
            if (isset($data[$field])) {
            }
        }
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class WithNegationBinaryOpPreviousIf
{
    public function run($a, $b, $data, $field)
    {
        if (!($a && $b) && isset($data[$field])) {
        }
    }
}

?>
