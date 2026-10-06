<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class UnionObjectNullable
{
    public function run(null|\DateTime $dateTime)
    {
        return empty($dateTime);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class UnionObjectNullable
{
    public function run(null|\DateTime $dateTime)
    {
        return !$dateTime instanceof \DateTime;
    }
}

?>
