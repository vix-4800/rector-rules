<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeObjectToBeConfigured;

final class SkipNonEntity extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $mockObject = $this->createConfiguredMock(SomeObjectToBeConfigured::class, [
            'getName'=> 'John',
        ]);
    }
}
