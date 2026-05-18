<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2FindOneIdShortcutRector;

final class Yii2FindOneIdShortcutRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesSingleIdArrayWithScalar(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsNonSingleIdArray(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return Yii2FindOneIdShortcutRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'variable id' => [
            <<<'PHP'
            <?php

            $model = User::findOne(['id' => $id]);
            PHP,
            <<<'PHP'
            <?php

            $model = User::findOne($id);
            PHP,
        ];

        yield 'expression id' => [
            <<<'PHP'
            <?php

            $model = App\Model\User::findOne(['id' => (int) $request->get('id')]);
            PHP,
            <<<'PHP'
            <?php

            $model = App\Model\User::findOne((int) $request->get('id'));
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'composite criteria' => [
            <<<'PHP'
            <?php

            $model = User::findOne(['id' => $id, 'status' => 1]);
            PHP,
        ];

        yield 'different key and direct scalar' => [
            <<<'PHP'
            <?php

            $model = User::findOne(['uuid' => $uuid]);
            $same = User::findOne($id);
            PHP,
        ];
    }
}
