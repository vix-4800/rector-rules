<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class DontChangeAlreadyReturnTyped
{
    public function accept()
    {
        return $this->something() || $this->somethingElse();
    }

    private function something(): bool
    {
        return true;
    }

    private function somethingElse(): bool
    {
        return true;
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

class DontChangeAlreadyReturnTyped
{
    public function accept()
    {
        if ($this->something()) {
            return true;
        }
        return $this->somethingElse();
    }

    private function something(): bool
    {
        return true;
    }

    private function somethingElse(): bool
    {
        return true;
    }
}

?>
