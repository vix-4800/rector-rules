<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class MultipleBinaryOrStartWithMethodCall
{
    public function run($a, $b)
    {
        return $this->execute() || $a || $b;
    }

    private function execute() {}
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class MultipleBinaryOrStartWithMethodCall
{
    public function run($a, $b)
    {
        if ($this->execute()) {
            return true;
        }
        if ($a) {
            return true;
        }
        return (bool) $b;
    }

    private function execute() {}
}

?>
