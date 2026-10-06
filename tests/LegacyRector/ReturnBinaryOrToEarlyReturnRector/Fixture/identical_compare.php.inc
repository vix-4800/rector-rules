<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

final class IdenticalCompare
{
    public function accept()
    {
        return $this->something() === 1 || !$this->somethingElse();
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

final class IdenticalCompare
{
    public function accept()
    {
        if ($this->something() === 1) {
            return true;
        }
        return !$this->somethingElse();
    }
}

?>
