<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2FindOneFindAllShortcutRector;

final class Yii2FindOneFindAllShortcutRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesFindWhereTerminalCall(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsUnsafeChains(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return Yii2FindOneFindAllShortcutRector::class;
    }

    public static function provideChangedCases(): iterable
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

    public static function provideUnchangedCases(): iterable
    {
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
}
