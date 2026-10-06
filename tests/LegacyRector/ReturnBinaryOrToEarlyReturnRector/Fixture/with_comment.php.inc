<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

final class WithComment
{
    public function accept()
    {
        // a comment
        if (rand(0, 1)) {
            // another comment
            return $this->something() || $this->somethingElse();
        }

        // another next comment
        return 1;
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ReturnBinaryOrToEarlyReturnRector\Fixture;

final class WithComment
{
    public function accept()
    {
        // a comment
        if (rand(0, 1)) {
            // another comment
            if ($this->something()) {
                return true;
            }
            return (bool) $this->somethingElse();
        }

        // another next comment
        return 1;
    }
}

?>
