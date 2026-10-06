<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector\Fixture;

class ApplyAttributeToMethods
{
    public function login($username, $password): void
    {
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector\Fixture;

class ApplyAttributeToMethods
{
    public function login($username, #[\SensitiveParameter]
    $password): void
    {
    }
}

?>
