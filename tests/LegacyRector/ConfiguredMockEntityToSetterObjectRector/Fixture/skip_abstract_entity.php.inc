<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\AbstractEntityToBeConfigured;

final class SkipAbstractEntity extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $mockObject = $this->createConfiguredMock(AbstractEntityToBeConfigured::class, [
            'getName'=> 'John',
        ]);
    }
}
