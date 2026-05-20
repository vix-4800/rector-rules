<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector;

final class ReplaceMultipleEqualWithInArrayRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesRepeatedComparisonsWithInArray(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsUnsupportedComparisonChains(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return ReplaceMultipleEqualWithInArrayRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'strict or chain' => [
            <<<'PHP'
            <?php

            if ($status === 'new' || $status === 'active' || $status === 'done') {
                return true;
            }
            PHP,
            <<<'PHP'
            <?php

            if (in_array($status, ['new', 'active', 'done'], true)) {
                return true;
            }
            PHP,
        ];

        yield 'loose or chain with variable on right' => [
            <<<'PHP'
            <?php

            if ('new' == $status || 'active' == $status || 'done' == $status) {
                return true;
            }
            PHP,
            <<<'PHP'
            <?php

            if (in_array($status, ['new', 'active', 'done'])) {
                return true;
            }
            PHP,
        ];

        yield 'not identical and chain' => [
            <<<'PHP'
            <?php

            if ($direction !== 'top' && $direction !== 'bottom' && $direction !== 'left') {
                return 'right';
            }
            PHP,
            <<<'PHP'
            <?php

            if (!in_array($direction, ['top', 'bottom', 'left'], true)) {
                return 'right';
            }
            PHP,
        ];

        yield 'mixed strictness becomes strict' => [
            <<<'PHP'
            <?php

            if ($type === 1 || $type == 2 || $type === 3) {
                return true;
            }
            PHP,
            <<<'PHP'
            <?php

            if (in_array($type, [1, 2, 3], true)) {
                return true;
            }
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'simple null empty check' => [
            <<<'PHP'
            <?php

            if ($value === null || $value === '') {
                return false;
            }
            PHP,
        ];

        yield 'different variables' => [
            <<<'PHP'
            <?php

            if ($a === 'x' || $b === 'y') {
                return true;
            }
            PHP,
        ];

        yield 'unsupported mixed boolean operator' => [
            <<<'PHP'
            <?php

            if ($status === 'new' || ($status === 'active' && $enabled)) {
                return true;
            }
            PHP,
        ];

        yield 'default threshold skips two comparisons' => [
            <<<'PHP'
            <?php

            if ($status === 'new' || $status === 'active') {
                return true;
            }
            PHP,
        ];
    }
}
