<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class NotObjectCallInLast
{
    public function accept()
    {
        return $this->something() || true;
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class NotObjectCallInLast
{
    public function accept()
    {
        if ($this->something()) {
            return true;
        }
        return true;
    }
}

?>
