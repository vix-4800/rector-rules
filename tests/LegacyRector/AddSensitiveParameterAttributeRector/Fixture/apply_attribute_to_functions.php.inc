<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector\Fixture;

function login($username, $password): void
{
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector\Fixture;

function login($username, #[\SensitiveParameter]
$password): void
{
}

?>
