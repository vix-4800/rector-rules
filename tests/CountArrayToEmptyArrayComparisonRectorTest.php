<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\LegacyRector\CountArrayToEmptyArrayComparisonRector;

/**
 * @internal
 */
#[CoversClass(CountArrayToEmptyArrayComparisonRector::class)]
final class CountArrayToEmptyArrayComparisonRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesCountComparisonsCases')]
    #[Test]
    public function replacesCountComparisons(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideReplacesCountComparisonsCases(): iterable
    {
        yield 'local array comparisons' => [
            <<<'PHP'
                <?php

                $items = [];
                count($items) === 0;
                0 === count($items);
                count($items) > 0;
                0 < count($items);
                PHP,
            <<<'PHP'
                <?php

                $items = [];
                $items === [];
                [] === $items;
                $items !== [];
                [] !== $items;
                PHP,
        ];

        yield 'method-returned array and count conditions' => [
            <<<'PHP'
                <?php

                final class ItemProvider
                {
                    public function hasItems(): bool
                    {
                        if (count($this->getItems())) {
                            return true;
                        }

                        return !count($this->getItems());
                    }

                    private function getItems(): array
                    {
                        return [];
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                final class ItemProvider
                {
                    public function hasItems(): bool
                    {
                        if ($this->getItems() !== []) {
                            return true;
                        }

                        return $this->getItems() === [];
                    }

                    private function getItems(): array
                    {
                        return [];
                    }
                }
                PHP,
        ];

        yield 'array parameter not identical comparison' => [
            <<<'PHP'
                <?php

                function hasItems(array $items): bool
                {
                    return count($items) !== 0;
                }
                PHP,
            <<<'PHP'
                <?php

                function hasItems(array $items): bool
                {
                    return $items !== [];
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsupportedCountCases')]
    #[Test]
    public function skipsUnsupportedCountCases(string $input): void
    {
        $this->doTestCode($input);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideSkipsUnsupportedCountCases(): iterable
    {
        yield 'countable object' => [
            <<<'PHP'
                <?php

                final class ItemCollection implements Countable
                {
                    public function hasItems(): bool
                    {
                        return count($this) > 0;
                    }

                    public function count(): int
                    {
                        return 0;
                    }
                }
                PHP,
        ];

        yield 'while condition' => [
            <<<'PHP'
                <?php

                function clearItems(array $items): void
                {
                    while (count($items)) {
                        $items = [];
                    }
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return CountArrayToEmptyArrayComparisonRector::class;
    }
}
