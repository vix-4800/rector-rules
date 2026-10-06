<?php

namespace Vix\RectorRules\Tests\LegacyRector\SwitchNegatedTernaryRector\Fixture;

class Fixture
{
    public function run(bool $upper, string $name)
    {
        return ! $upper
            ? $name
            : strtoupper($name);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\SwitchNegatedTernaryRector\Fixture;

class Fixture
{
    public function run(bool $upper, string $name)
    {
        return $upper
            ? strtoupper($name)
            : $name;
    }
}

?>
