<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2AddPropertyTagsRector;

/**
 * @internal
 */
#[CoversClass(Yii2AddPropertyTagsRector::class)]
final class Yii2AddPropertyTagsConfiguredRectorTest extends AbstractRuleTestCase
{
    #[Test]
    public function refinesTagKindsAndRemovesUnresolvedTags(): void
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

                    /**
                     * @property string $name
                     * @property int $obsolete
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
                     * @property-read string $name
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
        );
    }

    protected function getRuleClass(): string
    {
        return Yii2AddPropertyTagsRector::class;
    }

    protected function getRuleConfiguration(): array
    {
        return [
            Yii2AddPropertyTagsRector::REFINE_PROPERTY_TAG_KINDS => true,
            Yii2AddPropertyTagsRector::REMOVE_UNRESOLVED_PROPERTY_TAGS => true,
        ];
    }
}
