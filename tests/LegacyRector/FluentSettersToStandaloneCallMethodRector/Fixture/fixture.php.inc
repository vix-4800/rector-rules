<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SomeSetterClass;

final class SomeClass
{
    public function setup()
    {
        return (new SomeSetterClass())
            ->setName('John')
            ->setSurname('Doe');
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SomeSetterClass;

final class SomeClass
{
    public function setup()
    {
        $someSetterClass = new SomeSetterClass();
        $someSetterClass->setName('John');
        $someSetterClass->setSurname('Doe');
        return $someSetterClass;
    }
}

?>
