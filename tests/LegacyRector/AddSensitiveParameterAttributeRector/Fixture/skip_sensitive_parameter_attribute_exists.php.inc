<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddSensitiveParameterAttributeRector\Fixture;

class SkipSensitiveParameterAttributeExists
{
    public function login($username, #[\SensitiveParameter]
    $password): void
    {
    }
}

?>
