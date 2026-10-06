<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use PHPUnit\Framework\TestCase;
use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SomeSetterClass;

final class SkipMocks extends TestCase
{
    public function test()
    {
        $this->createMock(SomeSetterClass::class)
            ->expects($this->once())
            ->method('some');
    }
}
