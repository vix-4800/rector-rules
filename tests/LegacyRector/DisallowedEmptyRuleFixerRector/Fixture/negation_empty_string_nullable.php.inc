<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NegationEmptyStringNullable
{
    public function run(string|null $value)
    {
        return ! empty($value);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class NegationEmptyStringNullable
{
    public function run(string|null $value)
    {
        return $value !== null && $value !== '' && $value !== '0';
    }
}

?>
