<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\Yii2RedundantActiveRecordSelfLookupRector;

final class Yii2RedundantActiveRecordSelfLookupRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testReplacesRedundantSelfLookup(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsNonCurrentModelLookup(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return Yii2RedundantActiveRecordSelfLookupRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'self findOne by current id' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return self::findOne($this->id);
                }
            }
            PHP,
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return $this;
                }
            }
            PHP,
        ];

        yield 'current class findOne by current id' => [
            <<<'PHP'
            <?php

            namespace App\Models;

            class ActiveRecord extends \yii\db\ActiveRecord
            {
            }

            final class User extends \yii\db\ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return User::findOne($this->id);
                }
            }
            PHP,
            <<<'PHP'
            <?php

            namespace App\Models;

            class ActiveRecord extends \yii\db\ActiveRecord
            {
            }

            final class User extends \yii\db\ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return $this;
                }
            }
            PHP,
        ];

        yield 'self findOne by current id array' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return self::findOne(['id' => $this->id]);
                }
            }
            PHP,
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return $this;
                }
            }
            PHP,
        ];

        yield 'find where one by current id' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return self::find()->where(['id' => $this->id])->one();
                }
            }
            PHP,
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return $this;
                }
            }
            PHP,
        ];

        yield 'find where one by current id with limit' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return self::find()->where(['id' => $this->id])->limit(1)->one();
                }
            }
            PHP,
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): self
                {
                    return $this;
                }
            }
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'different id source' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function findOther(int $id): ?self
                {
                    return self::findOne($id);
                }
            }
            PHP,
        ];

        yield 'different model' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getProfile(): ?Profile
                {
                    return Profile::findOne($this->id);
                }
            }
            PHP,
        ];

        yield 'composite criteria' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): ?self
                {
                    return self::find()->where(['id' => $this->id, 'status' => 1])->one();
                }
            }
            PHP,
        ];

        yield 'limit zero' => [
            <<<'PHP'
            <?php

            use yii\db\ActiveRecord;

            final class User extends ActiveRecord
            {
                public function getCurrentModel(): ?self
                {
                    return self::find()->where(['id' => $this->id])->limit(0)->one();
                }
            }
            PHP,
        ];

        yield 'non active record class' => [
            <<<'PHP'
            <?php

            final class User
            {
                public function getCurrentModel(): self
                {
                    return self::findOne($this->id);
                }
            }
            PHP,
        ];
    }
}
