<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2FindOneIdShortcutRector;

/**
 * @internal
 */
#[CoversClass(Yii2FindOneIdShortcutRector::class)]
final class Yii2FindOneIdShortcutRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesSingleIdArrayWithScalarCases')]
    #[Test]
    public function replacesSingleIdArrayWithScalar(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesSingleIdArrayWithScalarCases(): iterable
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

    #[DataProvider('provideSkipsNonSingleIdArrayCases')]
    #[Test]
    public function skipsNonSingleIdArray(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsNonSingleIdArrayCases(): iterable
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

    protected function getRuleClass(): string
    {
        return Yii2FindOneIdShortcutRector::class;
    }
}
