<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\NullableBoolReturnToFalseRector;

/**
 * @internal
 */
#[CoversClass(NullableBoolReturnToFalseRector::class)]
final class NullableBoolReturnToFalseRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangesNullableBoolReturnToFalseCases')]
    #[Test]
    public function changesNullableBoolReturnToFalse(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideChangesNullableBoolReturnToFalseCases(): iterable
    {
        yield 'function and method returns' => [
            <<<'PHP'
                <?php

                function isReady(): ?bool
                {
                    if (rand(0, 1)) {
                        return null;
                    }

                    return true;
                }

                final class Foo
                {
                    public function isEnabled(): ?bool
                    {
                        foreach ([1] as $value) {
                            if ($value === 1) {
                                return null;
                            }
                        }

                        return false;
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                function isReady(): bool
                {
                    if (rand(0, 1)) {
                        return false;
                    }

                    return true;
                }

                final class Foo
                {
                    public function isEnabled(): bool
                    {
                        foreach ([1] as $value) {
                            if ($value === 1) {
                                return false;
                            }
                        }

                        return false;
                    }
                }
                PHP,
        ];

        yield 'nested closure return is untouched while outer type changes' => [
            <<<'PHP'
                <?php

                function hasAccess(): ?bool
                {
                    $callback = static function (): ?bool {
                        return null;
                    };

                    return null;
                }
                PHP,
            <<<'PHP'
                <?php

                function hasAccess(): bool
                {
                    $callback = static function (): ?bool {
                        return null;
                    };

                    return false;
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsNonNullableBoolReturnsCases')]
    #[Test]
    public function skipsNonNullableBoolReturns(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsNonNullableBoolReturnsCases(): iterable
    {
        yield 'nullable string and plain bool' => [
            <<<'PHP'
                <?php

                function name(): ?string
                {
                    return null;
                }

                function active(): bool
                {
                    return false;
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return NullableBoolReturnToFalseRector::class;
    }
}
