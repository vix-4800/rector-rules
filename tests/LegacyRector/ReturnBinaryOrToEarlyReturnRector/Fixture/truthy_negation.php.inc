<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class TruthyNegation
{
    public function accept()
    {
        return !$this->something() || !$this->somethingelse();
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class TruthyNegation
{
    public function accept()
    {
        if (!$this->something()) {
            return true;
        }
        return !$this->somethingelse();
    }
}

?>
