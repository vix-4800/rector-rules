<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\GetterSetterClass;

final class SkipGetterSetter
{
    public function setup()
    {
        return (new GetterSetterClass())
            ->getter()
            ->setName('John')
            ->setSurname('Doe');
    }
}
