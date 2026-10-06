<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class SkipNotObjectCall
{
    public function accept()
    {
        return $this->something || $this->somethingelse;
    }
}

?>
