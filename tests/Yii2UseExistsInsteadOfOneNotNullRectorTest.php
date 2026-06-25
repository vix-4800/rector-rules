<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2UseExistsInsteadOfOneNotNullRector;

/**
 * @internal
 */
#[CoversNothing]
final class Yii2UseExistsInsteadOfOneNotNullRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesOneNullComparisonsWithExistsCases')]
    public function testReplacesOneNullComparisonsWithExists(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesOneNullComparisonsWithExistsCases(): iterable
    {
        yield 'one not null and null mirrored' => [
            <<<'PHP'
                <?php

                $a = User::find()->where(['id' => $id])->one() !== null;
                $b = null !== User::find()->where(['id' => $id])->one();
                PHP,
            <<<'PHP'
                <?php

                $a = User::find()->where(['id' => $id])->exists();
                $b = User::find()->where(['id' => $id])->exists();
                PHP,
        ];

        yield 'one equal null and null mirrored' => [
            <<<'PHP'
                <?php

                $a = User::find()->where(['id' => $id])->one() === null;
                $b = null === User::find()->where(['id' => $id])->one();
                PHP,
            <<<'PHP'
                <?php

                $a = !User::find()->where(['id' => $id])->exists();
                $b = !User::find()->where(['id' => $id])->exists();
                PHP,
        ];
    }

    #[DataProvider('provideSkipsOtherComparisonsCases')]
    public function testSkipsOtherComparisons(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsOtherComparisonsCases(): iterable
    {
        yield 'loose comparison and different method' => [
            <<<'PHP'
                <?php

                $a = User::find()->where(['id' => $id])->one() != null;
                $b = User::find()->where(['id' => $id])->all() !== null;
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2UseExistsInsteadOfOneNotNullRector::class;
    }
}
