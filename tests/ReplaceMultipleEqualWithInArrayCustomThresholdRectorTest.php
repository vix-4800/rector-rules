<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector;

final class ReplaceMultipleEqualWithInArrayCustomThresholdRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesComparisonsWhenCustomThresholdIsMet(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    protected function getRuleClass(): string
    {
        return ReplaceMultipleEqualWithInArrayRector::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRuleConfiguration(): array
    {
        return [ReplaceMultipleEqualWithInArrayRector::THRESHOLD => 2];
    }

    public static function provideChangedCases(): iterable
    {
        yield 'custom threshold allows two strict comparisons' => [
            <<<'PHP'
            <?php

            if ($status === 'new' || $status === 'active') {
                return true;
            }
            PHP,
            <<<'PHP'
            <?php

            if (in_array($status, ['new', 'active'], true)) {
                return true;
            }
            PHP,
        ];
    }
}
