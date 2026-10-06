<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Expression;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2FindAllIdShortcutRector;

/**
 * @internal
 */
#[CoversClass(Yii2FindAllIdShortcutRector::class)]
final class Yii2FindAllIdShortcutRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesSingleIdArrayWithScalarCases')]
    #[Test]
    public function replacesSingleIdArrayWithScalar(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesSingleIdArrayWithScalarCases(): iterable
    {
        yield 'variable id list' => [
            <<<'PHP'
                <?php

                $models = User::findAll(['id' => $ids]);
                PHP,
            <<<'PHP'
                <?php

                $models = User::findAll($ids);
                PHP,
        ];

        yield 'array expression id list' => [
            <<<'PHP'
                <?php

                $models = User::findAll(['id' => array_map('intval', $ids)]);
                PHP,
            <<<'PHP'
                <?php

                $models = User::findAll(array_map('intval', $ids));
                PHP,
        ];
    }

    #[DataProvider('provideSkipsNonSingleIdArrayCases')]
    #[Test]
    public function skipsNonSingleIdArray(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsNonSingleIdArrayCases(): iterable
    {
        yield 'other criteria' => [
            <<<'PHP'
                <?php

                $models = User::findAll(['status' => 1]);
                PHP,
        ];

        yield 'composite criteria and direct scalar' => [
            <<<'PHP'
                <?php

                $models = User::findAll(['id' => $ids, 'status' => 1]);
                $same = User::findAll($ids);
                PHP,
        ];
    }

    #[DataProvider('provideArgumentPlaceholderCases')]
    #[Test]
    public function skipsArgumentPlaceholders(string $input): void
    {
        // PHPStan's scope resolver does not yet support partial application syntax.
        $nodes = (new ParserFactory())->createForNewestSupportedVersion()->parse($input);
        self::assertIsArray($nodes);
        $node = $nodes[0];
        self::assertInstanceOf(Expression::class, $node);
        self::assertInstanceOf(Expr\Assign::class, $node->expr);
        $node = $node->expr->expr;
        $printer = new Standard();
        $before = $printer->prettyPrintFile($nodes);
        $rule = $this->make(Yii2FindAllIdShortcutRector::class);

        self::assertNull($rule->refactor($node));
        self::assertSame($before, $printer->prettyPrintFile($nodes));
    }

    public static function provideArgumentPlaceholderCases(): iterable
    {
        yield 'partial application' => [
            <<<'PHP'
                <?php

                $callback = User::findAll(?);
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2FindAllIdShortcutRector::class;
    }
}
