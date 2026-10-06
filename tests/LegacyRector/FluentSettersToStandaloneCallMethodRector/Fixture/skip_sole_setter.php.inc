<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SomeSetterClass;

final class SkipSoleSetter
{
    public function setup()
    {
        return (new SomeSetterClass())
            ->setName('John');
    }
}
