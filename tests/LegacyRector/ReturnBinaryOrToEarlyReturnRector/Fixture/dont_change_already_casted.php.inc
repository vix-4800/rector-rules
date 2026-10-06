<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class DontChangeAlreadyCasted
{
    public function accept()
    {
        return $this->something() || (bool) $this->somethingelse();
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class DontChangeAlreadyCasted
{
    public function accept()
    {
        if ($this->something()) {
            return true;
        }
        return (bool) $this->somethingelse();
    }
}

?>
