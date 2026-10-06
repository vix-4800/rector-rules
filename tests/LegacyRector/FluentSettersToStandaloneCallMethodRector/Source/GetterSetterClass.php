<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source;

final class GetterSetterClass
{
    public function getter()
    {
        return new SomeSetterClass();
    }
}
