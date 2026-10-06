<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2RedundantActiveRecordSelfLookupRector;

/**
 * @internal
 */
#[CoversClass(Yii2RedundantActiveRecordSelfLookupRector::class)]
final class Yii2RedundantActiveRecordSelfLookupRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesRedundantSelfLookupCases')]
    #[Test]
    public function replacesRedundantSelfLookup(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesRedundantSelfLookupCases(): iterable
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

    #[DataProvider('provideSkipsNonCurrentModelLookupCases')]
    #[Test]
    public function skipsNonCurrentModelLookup(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsNonCurrentModelLookupCases(): iterable
    {
        yield 'terminal callable' => [
            <<<'PHP'
                <?php

                class PartialLookup extends \yii\db\ActiveRecord
                {
                    public function lookup()
                    {
                        return self::find()->where(['id' => $this->id])->one(...);
                    }
                }
                PHP,
        ];

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

    #[DataProvider('provideArgumentPlaceholderCases')]
    #[Test]
    public function skipsArgumentPlaceholders(string $input): void
    {
        // PHPStan's scope resolver does not yet support partial application syntax.
        $nodes = (new ParserFactory())->createForNewestSupportedVersion()->parse($input);
        self::assertIsArray($nodes);
        $nodes = (new NodeTraverser(new NameResolver()))->traverse($nodes);
        $node = $nodes[0];
        self::assertInstanceOf(Class_::class, $node);
        $printer = new Standard();
        $before = $printer->prettyPrintFile($nodes);
        $rule = $this->make(Yii2RedundantActiveRecordSelfLookupRector::class);

        self::assertNull($rule->refactor($node));
        self::assertSame($before, $printer->prettyPrintFile($nodes));
    }

    public static function provideArgumentPlaceholderCases(): iterable
    {
        yield 'partial findOne' => [
            <<<'PHP'
                <?php

                class PartialLookup extends \yii\db\ActiveRecord
                {
                    public function lookup()
                    {
                        return self::findOne(?);
                    }
                }
                PHP,
        ];

        yield 'partial where' => [
            <<<'PHP'
                <?php

                class PartialLookup extends \yii\db\ActiveRecord
                {
                    public function lookup()
                    {
                        return self::find()->where(?)->one();
                    }
                }
                PHP,
        ];

        yield 'partial limit' => [
            <<<'PHP'
                <?php

                class PartialLookup extends \yii\db\ActiveRecord
                {
                    public function lookup()
                    {
                        return self::find()->where(['id' => $this->id])->limit(?)->one();
                    }
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2RedundantActiveRecordSelfLookupRector::class;
    }
}
