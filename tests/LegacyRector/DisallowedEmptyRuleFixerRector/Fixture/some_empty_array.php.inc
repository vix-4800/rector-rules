<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class SomeEmptyArray
{
    public function run(array $items)
    {
        return empty($items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class SomeEmptyArray
{
    public function run(array $items)
    {
        return $items === [];
    }
}

?>
