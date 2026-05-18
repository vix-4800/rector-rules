<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\TestCase;
use Vix\RectorRules\AddTypedClassConstantRector;

final class PackageConfigTest extends TestCase
{
    public function testConfigReturnsClosure(): void
    {
        self::assertInstanceOf(\Closure::class, require __DIR__ . '/../config/rules.php');
    }

    public function testAutoloadsRuleClasses(): void
    {
        self::assertTrue(class_exists(AddTypedClassConstantRector::class));
    }
}
