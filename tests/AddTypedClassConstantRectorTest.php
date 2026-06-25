<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\AddTypedClassConstantRector;

/**
 * @internal
 */
#[CoversNothing]
final class AddTypedClassConstantRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideAddsInferredTypeCases')]
    public function testAddsInferredType(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideAddsInferredTypeCases(): iterable
    {
        yield 'scalar constants' => [
            <<<'PHP'
                <?php

                final class Foo
                {
                    public const MAX = 10;
                    protected const NAME = 'demo';
                    private const ENABLED = true;
                    public const RATE = 1.5;
                    public const LIST = ['a', 'b'];
                }
                PHP,
            <<<'PHP'
                <?php

                final class Foo
                {
                    public const int MAX = 10;
                    protected const string NAME = 'demo';
                    private const bool ENABLED = true;
                    public const float RATE = 1.5;
                    public const array LIST = ['a', 'b'];
                }
                PHP,
        ];

        yield 'negative numbers and grouped same type constants' => [
            <<<'PHP'
                <?php

                interface Foo
                {
                    public const MIN = -10;
                    public const A = 1, B = 2;
                    public const LEFT = 'left', RIGHT = 'right';
                }
                PHP,
            <<<'PHP'
                <?php

                interface Foo
                {
                    public const int MIN = -10;
                    public const int A = 1, B = 2;
                    public const string LEFT = 'left', RIGHT = 'right';
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsafeCasesCases')]
    public function testSkipsUnsafeCases(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsafeCasesCases(): iterable
    {
        yield 'already typed' => [
            <<<'PHP'
                <?php

                final class Foo
                {
                    public const int MAX = 10;
                }
                PHP,
        ];

        yield 'null and expressions' => [
            <<<'PHP'
                <?php

                final class Foo
                {
                    public const NOTHING = null;
                    public const CALCULATED = 1 + 2;
                    public const ALIAS = self::CALCULATED;
                }
                PHP,
        ];

        yield 'mixed grouped values' => [
            <<<'PHP'
                <?php

                final class Foo
                {
                    public const ID = 1, NAME = 'name';
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return AddTypedClassConstantRector::class;
    }
}
