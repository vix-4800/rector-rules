<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SetNameCaller;

class SkipReturnDifferentObject
{
     public function setup()
     {
          return (new SetNameCaller())
               ->setName('John')
               ->setSurname('Doe');
     }
}
