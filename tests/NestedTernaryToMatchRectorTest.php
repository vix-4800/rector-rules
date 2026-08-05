<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\LegacyRector\NestedTernaryToMatchRector;

/**
 * @internal
 */
#[CoversClass(NestedTernaryToMatchRector::class)]
final class NestedTernaryToMatchRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideConvertsNestedTernariesCases')]
    #[Test]
    public function convertsNestedTernaries(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideConvertsNestedTernariesCases(): iterable
    {
        yield 'unrelated conditions use match true' => [
            <<<'PHP'
                <?php

                $result = $value > 10 ? 'greater than 10' : ($value === 10 ? 'equal to 10' : 'less than 10');
                PHP,
            <<<'PHP'
                <?php

                $result = match (true) {
                    $value > 10 => 'greater than 10',
                    $value === 10 => 'equal to 10',
                    default => 'less than 10',
                };
                PHP,
        ];

        yield 'identical comparisons for one variable use match subject' => [
            <<<'PHP'
                <?php

                $result = $value === 10 ? 'equal to 10' : ($value === 5 ? 'equal to 5' : 'other value');
                PHP,
            <<<'PHP'
                <?php

                $result = match ($value) {
                    10 => 'equal to 10',
                    5 => 'equal to 5',
                    default => 'other value',
                };
                PHP,
        ];

        yield 'identical comparisons for different variables use match true' => [
            <<<'PHP'
                <?php

                $result = $value === 10 ? 'equal to 10' : ($nextValue === 5 ? 'equal to 5' : 'other value');
                PHP,
            <<<'PHP'
                <?php

                $result = match (true) {
                    $value === 10 => 'equal to 10',
                    $nextValue === 5 => 'equal to 5',
                    default => 'other value',
                };
                PHP,
        ];
    }

    #[Test]
    public function skipsShortTernaries(): void
    {
        $this->doTestCode(<<<'PHP'
            <?php

            $result = $value > 10 ? 'greater than 10' : ($value === 10 ?: 'less than 10');
            PHP);
    }

    #[Test]
    public function skipsTruthyNonBooleanConditions(): void
    {
        $this->doTestCode(<<<'PHP'
            <?php

            $result = $value ? 'value is truthy' : ($nextValue ? 'next value is truthy' : 'both are falsy');
            PHP);
    }

    protected function getRuleClass(): string
    {
        return NestedTernaryToMatchRector::class;
    }
}
