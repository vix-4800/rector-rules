<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Vix\RectorRules\AddTypedClassConstantRector;

/**
 * @internal
 */
#[CoversClass(AddTypedClassConstantRector::class)]
final class PackageConfigTest extends TestCase
{
    #[Test]
    public function configReturnsClosure(): void
    {
        $this->assertInstanceOf(Closure::class, require __DIR__ . '/../config/rules.php');
    }

    #[Test]
    public function autoloadsRuleClasses(): void
    {
        $this->assertTrue(class_exists(AddTypedClassConstantRector::class));
    }
}
