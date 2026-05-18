<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2UseExistsInsteadOfCountRector;

final class Yii2UseExistsInsteadOfCountRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesCountComparisonsWithExists(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsUnsupportedCountComparisons(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return Yii2UseExistsInsteadOfCountRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'positive comparisons' => [
            <<<'PHP'
            <?php

            $a = User::find()->where(['active' => 1])->count() > 0;
            $b = User::find()->where(['active' => 1])->count() >= 1;
            $c = User::find()->where(['active' => 1])->count() != 0;
            $d = User::find()->where(['active' => 1])->count() !== 0;
            PHP,
            <<<'PHP'
            <?php

            $a = User::find()->where(['active' => 1])->exists();
            $b = User::find()->where(['active' => 1])->exists();
            $c = User::find()->where(['active' => 1])->exists();
            $d = User::find()->where(['active' => 1])->exists();
            PHP,
        ];

        yield 'negative comparisons' => [
            <<<'PHP'
            <?php

            $a = User::find()->where(['active' => 1])->count() === 0;
            $b = User::find()->where(['active' => 1])->count() == 0;
            $c = User::find()->where(['active' => 1])->count() < 1;
            $d = User::find()->where(['active' => 1])->count() <= 0;
            PHP,
            <<<'PHP'
            <?php

            $a = !User::find()->where(['active' => 1])->exists();
            $b = !User::find()->where(['active' => 1])->exists();
            $c = !User::find()->where(['active' => 1])->exists();
            $d = !User::find()->where(['active' => 1])->exists();
            PHP,
        ];

        yield 'mirrored comparisons keep current rule semantics' => [
            <<<'PHP'
            <?php

            $a = 0 < User::find()->where(['active' => 1])->count();
            $b = 1 <= User::find()->where(['active' => 1])->count();
            PHP,
            <<<'PHP'
            <?php

            $a = User::find()->where(['active' => 1])->exists();
            $b = User::find()->where(['active' => 1])->exists();
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'unsupported numbers' => [
            <<<'PHP'
            <?php

            $a = User::find()->count() > 1;
            $b = User::find()->count() >= 2;
            PHP,
        ];

        yield 'not count method' => [
            <<<'PHP'
            <?php

            $a = User::find()->total() > 0;
            PHP,
        ];
    }
}
