<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Fixture;

use PHPUnit\Framework\TestCase;
use Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source\SomeSetterClass;

final class SkipMocksOnVariable extends TestCase
{
    public function test()
    {
        $someVariable = $this->createMock(SomeSetterClass::class);
        $someVariable->expects($this->once())
            ->method('some')
            ->with(1);
    }
}
