<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2UseExistsInsteadOfOneNotNullRector;

/**
 * @internal
 */
#[CoversClass(Yii2UseExistsInsteadOfOneNotNullRector::class)]
final class Yii2UseExistsInsteadOfOneNotNullRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesOneNullComparisonsWithExistsCases')]
    #[Test]
    public function replacesOneNullComparisonsWithExists(string $input, string $expected): void
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
    #[Test]
    public function skipsOtherComparisons(string $input): void
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
