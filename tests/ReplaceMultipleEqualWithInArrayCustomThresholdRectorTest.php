<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\ReplaceMultipleEqualWithInArrayRector;

/**
 * @internal
 */
#[CoversClass(ReplaceMultipleEqualWithInArrayRector::class)]
final class ReplaceMultipleEqualWithInArrayCustomThresholdRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesComparisonsWhenCustomThresholdIsMetCases')]
    #[Test]
    public function replacesComparisonsWhenCustomThresholdIsMet(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesComparisonsWhenCustomThresholdIsMetCases(): iterable
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
}
