<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2FindAllIdShortcutRector;

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

    protected function getRuleClass(): string
    {
        return Yii2FindAllIdShortcutRector::class;
    }
}
