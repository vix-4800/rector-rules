<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2FindAllIdShortcutRector;

final class Yii2FindAllIdShortcutRectorTest extends AbstractRuleTestCase
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
        return Yii2FindAllIdShortcutRector::class;
    }

    public static function provideChangedCases(): iterable
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

    public static function provideUnchangedCases(): iterable
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
}
