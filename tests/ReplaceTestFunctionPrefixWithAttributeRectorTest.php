<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\LegacyRector\ReplaceTestFunctionPrefixWithAttributeRector;

/**
 * @internal
 */
#[CoversClass(ReplaceTestFunctionPrefixWithAttributeRector::class)]
final class ReplaceTestFunctionPrefixWithAttributeRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesTestPrefixesCases')]
    #[Test]
    public function replacesTestPrefixes(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideReplacesTestPrefixesCases(): iterable
    {
        yield 'camel case method' => [
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    public function testOnePlusOneShouldBeTwo(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    #[\PHPUnit\Framework\Attributes\Test]
                    public function onePlusOneShouldBeTwo(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
        ];

        yield 'snake case method' => [
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    public function test_one_plus_one_should_be_two(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    #[\PHPUnit\Framework\Attributes\Test]
                    public function one_plus_one_should_be_two(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
        ];

        yield 'test underscore method keeps its name' => [
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    public function test_(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    #[\PHPUnit\Framework\Attributes\Test]
                    public function test_(): void
                    {
                        $this->assertSame(2, 1 + 1);
                    }
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsupportedMethodsCases')]
    #[Test]
    public function skipsUnsupportedMethods(string $input): void
    {
        $this->doTestCode($input);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideSkipsUnsupportedMethodsCases(): iterable
    {
        yield 'non test class' => [
            <<<'PHP'
                <?php

                final class Calculator
                {
                    public function testCalculation(): void
                    {
                    }
                }
                PHP,
        ];

        yield 'method already has test attribute' => [
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    #[\PHPUnit\Framework\Attributes\Test]
                    public function testCalculation(): void
                    {
                    }
                }
                PHP,
        ];

        yield 'private helper with test prefix' => [
            <<<'PHP'
                <?php

                final class CalculatorTest extends \PHPUnit\Framework\TestCase
                {
                    public function calculate(): int
                    {
                        return $this->testFixture();
                    }

                    private function testFixture(): int
                    {
                        return 2;
                    }
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return ReplaceTestFunctionPrefixWithAttributeRector::class;
    }
}
