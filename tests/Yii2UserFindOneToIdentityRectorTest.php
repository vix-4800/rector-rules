<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2UserFindOneToIdentityRector;

/**
 * @internal
 */
#[CoversNothing]
final class Yii2UserFindOneToIdentityRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesCurrentUserFindOneWithIdentityCases')]
    public function testReplacesCurrentUserFindOneWithIdentity(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesCurrentUserFindOneWithIdentityCases(): iterable
    {
        yield 'global user class' => [
            <<<'PHP'
                <?php

                $user = User::findOne(Yii::$app->user->id);
                PHP,
            <<<'PHP'
                <?php

                $user = Yii::$app->user->identity;
                PHP,
        ];

        yield 'namespaced user class and array wrapper' => [
            <<<'PHP'
                <?php

                $user = App\Model\User::findOne(['id' => Yii::$app->user->id]);
                PHP,
            <<<'PHP'
                <?php

                $user = Yii::$app->user->identity;
                PHP,
        ];

        yield 'array value without id key' => [
            <<<'PHP'
                <?php

                $user = User::findOne([Yii::$app->user->id]);
                PHP,
            <<<'PHP'
                <?php

                $user = Yii::$app->user->identity;
                PHP,
        ];
    }

    #[DataProvider('provideSkipsOtherFindOneCallsCases')]
    public function testSkipsOtherFindOneCalls(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsOtherFindOneCallsCases(): iterable
    {
        yield 'not user class' => [
            <<<'PHP'
                <?php

                $user = Admin::findOne(Yii::$app->user->id);
                PHP,
        ];

        yield 'other argument' => [
            <<<'PHP'
                <?php

                $user = User::findOne($id);
                $userByComposite = User::findOne(['id' => Yii::$app->user->id, 'status' => 1]);
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2UserFindOneToIdentityRector::class;
    }
}
