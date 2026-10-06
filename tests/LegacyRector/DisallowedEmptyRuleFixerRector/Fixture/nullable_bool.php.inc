<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NullableBool
{
    public function run(bool|null $value)
    {
        return empty($value);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NullableBool
{
    public function run(bool|null $value)
    {
        return $value !== true;
    }
}

?>
