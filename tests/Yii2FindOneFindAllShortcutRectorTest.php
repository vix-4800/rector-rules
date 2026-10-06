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
use Vix\RectorRules\Yii2\Yii2FindOneFindAllShortcutRector;

/**
 * @internal
 */
#[CoversClass(Yii2FindOneFindAllShortcutRector::class)]
final class Yii2FindOneFindAllShortcutRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesFindWhereTerminalCallCases')]
    #[Test]
    public function replacesFindWhereTerminalCall(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesFindWhereTerminalCallCases(): iterable
    {
        yield 'one by id uses scalar shortcut' => [
            <<<'PHP'
                <?php

                $model = User::find()->where(['id' => $id])->one();
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne($id);
                PHP,
        ];

        yield 'all by id uses scalar shortcut' => [
            <<<'PHP'
                <?php

                $models = User::find()->where(['id' => $ids])->all();
                PHP,
            <<<'PHP'
                <?php

                $models = User::findAll($ids);
                PHP,
        ];

        yield 'composite criteria stays array' => [
            <<<'PHP'
                <?php

                $model = User::find()->where(['id' => $id, 'status' => 1])->one();
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne(['id' => $id, 'status' => 1]);
                PHP,
        ];

        yield 'non array where argument stays as argument' => [
            <<<'PHP'
                <?php

                $models = User::find()->where($condition)->all();
                PHP,
            <<<'PHP'
                <?php

                $models = User::findAll($condition);
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsafeChainsCases')]
    #[Test]
    public function skipsUnsafeChains(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsafeChainsCases(): iterable
    {
        yield 'where callable' => [
            <<<'PHP'
                <?php

                $callback = User::find()->where(...)->one();
                PHP,
        ];

        yield 'terminal callable' => [
            <<<'PHP'
                <?php

                $callback = User::find()->where(['id' => $id])->one(...);
                PHP,
        ];

        yield 'find callable' => [
            <<<'PHP'
                <?php

                $callback = User::find(...)->where(['id' => $id])->one();
                PHP,
        ];

        yield 'limit before one' => [
            <<<'PHP'
                <?php

                $model = User::find()->where(['status' => 1])->limit(1)->one();
                PHP,
        ];

        yield 'andWhere chain' => [
            <<<'PHP'
                <?php

                $model = User::find()->where(['id' => $id])->andWhere(['status' => 1])->one();
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
        $rule = $this->make(Yii2FindOneFindAllShortcutRector::class);

        self::assertNull($rule->refactor($node));
        self::assertSame($before, $printer->prettyPrintFile($nodes));
    }

    public static function provideArgumentPlaceholderCases(): iterable
    {
        yield 'partial application' => [
            <<<'PHP'
                <?php

                $callback = User::find()->where(?)->one();
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2FindOneFindAllShortcutRector::class;
    }
}
