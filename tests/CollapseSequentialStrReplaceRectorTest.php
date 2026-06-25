<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\CollapseSequentialStrReplaceRector;

/**
 * @internal
 */
#[CoversClass(CollapseSequentialStrReplaceRector::class)]
final class CollapseSequentialStrReplaceRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideCollapsesSequentialStrReplaceCallsCases')]
    #[Test]
    public function collapsesSequentialStrReplaceCalls(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideCollapsesSequentialStrReplaceCallsCases(): iterable
    {
        yield 'method return chain' => [
            <<<'PHP'
                <?php

                final class PhoneNormalizer
                {
                    public function normalize(string $phone): string
                    {
                        $value = str_replace('+', '', $phone);
                        $value = str_replace(' ', '', $value);
                        $value = str_replace('-', '', $value);

                        return str_replace('(', '', $value);
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                final class PhoneNormalizer
                {
                    public function normalize(string $phone): string
                    {
                        return str_replace(['+', ' ', '-', '('], '', $phone);
                    }
                }
                PHP,
        ];

        yield 'existing array search and nested if' => [
            <<<'PHP'
                <?php

                function normalize(string $value, bool $enabled): string
                {
                    if ($enabled) {
                        $value = str_replace(['+', ' '], '', $value);

                        return str_replace('-', '', $value);
                    }

                    return $value;
                }
                PHP,
            <<<'PHP'
                <?php

                function normalize(string $value, bool $enabled): string
                {
                    if ($enabled) {
                        return str_replace(['+', ' ', '-'], '', $value);
                    }

                    return $value;
                }
                PHP,
        ];

        yield 'replacement variable' => [
            <<<'PHP'
                <?php

                $callback = static function (string $value, string $replacement): string {
                    $value = str_replace('_', $replacement, $value);

                    return str_replace('-', $replacement, $value);
                };
                PHP,
            <<<'PHP'
                <?php

                $callback = static function (string $value, string $replacement): string {
                    return str_replace(['_', '-'], $replacement, $value);
                };
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsafeStrReplaceChainsCases')]
    #[Test]
    public function skipsUnsafeStrReplaceChains(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsafeStrReplaceChainsCases(): iterable
    {
        yield 'different replacement' => [
            <<<'PHP'
                <?php

                function normalize(string $value): string
                {
                    $value = str_replace('_', '-', $value);

                    return str_replace('-', '', $value);
                }
                PHP,
        ];

        yield 'non string search and no assigned chain' => [
            <<<'PHP'
                <?php

                function normalize(string $value, array $search): string
                {
                    $value = str_replace($search, '', $value);

                    return str_replace('-', '', $value);
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return CollapseSequentialStrReplaceRector::class;
    }
}
