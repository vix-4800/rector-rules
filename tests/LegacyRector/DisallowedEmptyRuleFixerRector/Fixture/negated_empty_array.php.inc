<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NegatedEmptyArray
{
    public function run(array $items)
    {
        return ! empty($items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NegatedEmptyArray
{
    public function run(array $items)
    {
        return $items !== [];
    }
}

?>
