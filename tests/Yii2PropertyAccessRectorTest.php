<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2PropertyAccessRector;

final class Yii2PropertyAccessRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testChangesYiiUserGetterToProperty(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsNonYiiUserGetters(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return Yii2PropertyAccessRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'id and identity getters' => [
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run(): void
                {
                    $id = Yii::$app->user->getId();
                    $identity = Yii::$app->user->getIdentity();
                }
            }
            PHP,
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run(): void
                {
                    $id = Yii::$app->user->id;
                    $identity = Yii::$app->user->identity;
                }
            }
            PHP,
        ];

        yield 'nested call argument' => [
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run(): void
                {
                    $this->handle(Yii::$app->user->getId(), Yii::$app->user->getIdentity());
                }
            }
            PHP,
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run(): void
                {
                    $this->handle(Yii::$app->user->id, Yii::$app->user->identity);
                }
            }
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'other user methods' => [
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run(): void
                {
                    $value = Yii::$app->user->getStatus();
                }
            }
            PHP,
        ];

        yield 'different component or variable' => [
            <<<'PHP'
            <?php

            final class Foo
            {
                public function run($app): void
                {
                    $id = Yii::$app->admin->getId();
                    $other = $app->user->getId();
                }
            }
            PHP,
        ];
    }
}
