<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2AddPropertyTagsRector;

/**
 * @internal
 */
#[CoversClass(Yii2AddPropertyTagsRector::class)]
final class Yii2AddPropertyTagsRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('providePropertyTagCases')]
    #[Test]
    public function addsAndCorrectsPropertyTags(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function providePropertyTagCases(): iterable
    {
        yield 'accessor pairs and relations' => [
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace yii\db {
                    class ActiveQuery
                    {
                    }

                    class BaseActiveRecord extends \yii\base\BaseObject
                    {
                        protected function hasOne(string $class, array $link): ActiveQuery
                        {
                            return new ActiveQuery();
                        }

                        protected function hasMany(string $class, array $link): ActiveQuery
                        {
                            return new ActiveQuery();
                        }
                    }
                }

                namespace {
                    use yii\base\BaseObject;
                    use yii\db\BaseActiveRecord;

                    final class Settings extends BaseObject
                    {
                        public function getName(): string
                        {
                            return '';
                        }

                        public function setName(string $name): void
                        {
                        }

                        public function getCount(): int
                        {
                            return 0;
                        }

                        public function setState(string $state): void
                        {
                        }
                    }

                    final class PropertyTagsUser
                    {
                    }

                    final class PropertyTagsBook
                    {
                    }

                    final class PropertyTagsOrder extends BaseActiveRecord
                    {
                        public function getUser(): \yii\db\ActiveQuery
                        {
                            return $this->hasOne(PropertyTagsUser::class, ['id' => 'user_id']);
                        }

                        public function getBooks(): \yii\db\ActiveQuery
                        {
                            return $this->hasMany(PropertyTagsBook::class, ['order_id' => 'id']);
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace yii\db {
                    class ActiveQuery
                    {
                    }

                    class BaseActiveRecord extends \yii\base\BaseObject
                    {
                        protected function hasOne(string $class, array $link): ActiveQuery
                        {
                            return new ActiveQuery();
                        }

                        protected function hasMany(string $class, array $link): ActiveQuery
                        {
                            return new ActiveQuery();
                        }
                    }
                }

                namespace {
                    use yii\base\BaseObject;
                    use yii\db\BaseActiveRecord;

                    /**
                     * @property string $name
                     * @property-read int $count
                     * @property-write string $state
                     */
                    final class Settings extends BaseObject
                    {
                        public function getName(): string
                        {
                            return '';
                        }

                        public function setName(string $name): void
                        {
                        }

                        public function getCount(): int
                        {
                            return 0;
                        }

                        public function setState(string $state): void
                        {
                        }
                    }

                    final class PropertyTagsUser
                    {
                    }

                    final class PropertyTagsBook
                    {
                    }

                    /**
                     * @property-read PropertyTagsUser|null $user
                     * @property-read PropertyTagsBook[] $books
                     */
                    final class PropertyTagsOrder extends BaseActiveRecord
                    {
                        public function getUser(): \yii\db\ActiveQuery
                        {
                            return $this->hasOne(PropertyTagsUser::class, ['id' => 'user_id']);
                        }

                        public function getBooks(): \yii\db\ActiveQuery
                        {
                            return $this->hasMany(PropertyTagsBook::class, ['order_id' => 'id']);
                        }
                    }
                }
                PHP,
        ];

        yield 'corrects type without changing other tags or the default tag kind' => [
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace {
                    use yii\base\BaseObject;

                    /**
                     * @property int $name Existing description.
                     * @mixin SomeMixin
                     * @author Jane Doe
                     */
                    final class Profile extends BaseObject
                    {
                        public function getName(): string
                        {
                            return '';
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace {
                    use yii\base\BaseObject;

                    /**
                     * @property string $name Existing description.
                     * @mixin SomeMixin
                     * @author Jane Doe
                     */
                    final class Profile extends BaseObject
                    {
                        public function getName(): string
                        {
                            return '';
                        }
                    }
                }
                PHP,
        ];
    }

    #[Test]
    public function skipsClassesWithCustomMagicAccessors(): void
    {
        $this->doTestCode(
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace {
                    use yii\base\BaseObject;

                    final class CustomProperties extends BaseObject
                    {
                        public function __get(string $name): mixed
                        {
                            return null;
                        }

                        public function getName(): string
                        {
                            return '';
                        }
                    }
                }
                PHP,
        );
    }

    #[Test]
    public function skipsClassesThatInheritCustomMagicAccessors(): void
    {
        $this->doTestCode(
            <<<'PHP'
                <?php

                namespace yii\base {
                    class BaseObject
                    {
                    }
                }

                namespace {
                    use yii\base\BaseObject;

                    class PropertyTagsCustomBase extends BaseObject
                    {
                        public function __get(string $name): mixed
                        {
                            return null;
                        }
                    }

                    final class PropertyTagsInheritedCustomProperties extends PropertyTagsCustomBase
                    {
                        public function getName(): string
                        {
                            return '';
                        }
                    }
                }
                PHP,
        );
    }

    #[Test]
    public function skipsNonYiiClassesWithMatchingShortParentNames(): void
    {
        $this->doTestCode(
            <<<'PHP'
                <?php

                namespace App {
                    class Component
                    {
                    }

                    final class Profile extends Component
                    {
                        public function getName(): string
                        {
                            return '';
                        }
                    }
                }
                PHP,
        );
    }

    protected function getRuleClass(): string
    {
        return Yii2AddPropertyTagsRector::class;
    }
}
