<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\LegacyRector\DisallowedEmptyRuleFixerRector;
use Vix\RectorRules\Tests\AbstractRuleTestCase;

/**
 * @internal
 */
#[CoversClass(DisallowedEmptyRuleFixerRector::class)]
final class DisallowedEmptyRuleFixerNonEmptyRectorTest extends AbstractRuleTestCase
{
    #[Test]
    public function treatsZeroStringAsNonEmpty(): void
    {
        $this->doTestCode(
            <<<'PHP'
                <?php

                function isEmpty(string $value): bool
                {
                    return empty($value);
                }
                PHP,
            <<<'PHP'
                <?php

                function isEmpty(string $value): bool
                {
                    return $value === '';
                }
                PHP,
        );
    }

    #[Test]
    public function treatsNegatedZeroStringAsNonEmpty(): void
    {
        $this->doTestCode(
            <<<'PHP'
                <?php

                function isNotEmpty(string $value): bool
                {
                    return !empty($value);
                }
                PHP,
            <<<'PHP'
                <?php

                function isNotEmpty(string $value): bool
                {
                    return $value !== '';
                }
                PHP,
        );
    }

    protected function getRuleClass(): string
    {
        return DisallowedEmptyRuleFixerRector::class;
    }

    /**
     * @return array<string, bool>
     */
    protected function getRuleConfiguration(): array
    {
        return [DisallowedEmptyRuleFixerRector::TREAT_AS_NON_EMPTY => true];
    }
}
