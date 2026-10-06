<?php

namespace Vix\RectorRules\Tests\LegacyRector\SimplifyIfElseToTernaryRector\Fixture;

class Fixture
{
    public function run()
    {
        if (empty($value)) {
            $this->arrayBuilt[][$key] = true;
        } else {
            $this->arrayBuilt[][$key] = $value;
        }
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\SimplifyIfElseToTernaryRector\Fixture;

class Fixture
{
    public function run()
    {
        $this->arrayBuilt[][$key] = empty($value) ? true : $value;
    }
}

?>
