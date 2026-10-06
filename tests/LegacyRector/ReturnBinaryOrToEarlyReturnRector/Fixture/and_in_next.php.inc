<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class AndInNext
{
    public function accept()
    {
        return $this->something() || $this->somethingelse() && $this->anotherelse() || $this->last();
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class AndInNext
{
    public function accept()
    {
        if ($this->something()) {
            return true;
        }
        if ($this->somethingelse() && $this->anotherelse()) {
            return true;
        }
        return (bool) $this->last();
    }
}

?>
