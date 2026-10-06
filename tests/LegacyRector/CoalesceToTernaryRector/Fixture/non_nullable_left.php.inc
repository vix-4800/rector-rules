<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class NonNullableLeft
{
    public function run(string $name)
    {
        return $name ?? 'tom';
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\CoalesceToTernaryRector\Fixture;

class NonNullableLeft
{
    public function run(string $name)
    {
        return $name ?: 'tom';
    }
}

?>
