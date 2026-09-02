<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\Yii2\Yii2MergeModelRulesRector;

/**
 * @internal
 */
#[CoversClass(Yii2MergeModelRulesRector::class)]
final class Yii2MergeModelRulesRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideMergesRulesCases')]
    #[Test]
    public function mergesRules(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideMergesRulesCases(): iterable
    {
        yield 'same validator' => [
            <<<'PHP'
                <?php

                namespace yii\base {
                    class Model
                    {
                    }
                }

                namespace App {
                    final class LoginForm extends \yii\base\Model
                    {
                        public function rules(): array
                        {
                            return [
                                ['login', 'required'],
                                ['password', 'required'],
                                ['rememberMe', 'boolean'],
                            ];
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace yii\base {
                    class Model
                    {
                    }
                }

                namespace App {
                    final class LoginForm extends \yii\base\Model
                    {
                        public function rules(): array
                        {
                            return [
                                [['login', 'password'], 'required'],
                                ['rememberMe', 'boolean'],
                            ];
                        }
                    }
                }
                PHP,
        ];

        yield 'same validator and options with distinct attributes' => [
            <<<'PHP'
                <?php

                namespace yii\base {
                    class Model
                    {
                    }
                }

                namespace App {
                    final class ProfileForm extends \yii\base\Model
                    {
                        public function rules(): array
                        {
                            return [
                                [['firstName', 'lastName'], 'string', 'max' => 255],
                                ['email', 'string', 'max' => 255],
                                ['firstName', 'string', 'max' => 255],
                            ];
                        }
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace yii\base {
                    class Model
                    {
                    }
                }

                namespace App {
                    final class ProfileForm extends \yii\base\Model
                    {
                        public function rules(): array
                        {
                            return [
                                [['firstName', 'lastName', 'email'], 'string', 'max' => 255],
                            ];
                        }
                    }
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsupportedRulesCases')]
    #[Test]
    public function skipsUnsupportedRules(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsupportedRulesCases(): iterable
    {
        yield 'different options' => [
            <<<'PHP'
                <?php

                class Form extends \yii\base\Model
                {
                    public function rules(): array
                    {
                        return [
                            ['name', 'string', 'max' => 100],
                            ['email', 'string', 'max' => 255],
                        ];
                    }
                }
                PHP,
        ];

        yield 'non-literal rule entry' => [
            <<<'PHP'
                <?php

                class Form extends \yii\base\Model
                {
                    public function rules(): array
                    {
                        return [
                            ['name', 'required'],
                            ...$this->additionalRules(),
                            ['email', 'required'],
                        ];
                    }
                }
                PHP,
        ];

        yield 'more than a return statement' => [
            <<<'PHP'
                <?php

                class Form extends \yii\base\Model
                {
                    public function rules(): array
                    {
                        $commonRules = ['name', 'required'];

                        return [
                            $commonRules,
                            ['email', 'required'],
                        ];
                    }
                }
                PHP,
        ];

        yield 'not a model' => [
            <<<'PHP'
                <?php

                class Form
                {
                    public function rules(): array
                    {
                        return [
                            ['name', 'required'],
                            ['email', 'required'],
                        ];
                    }
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return Yii2MergeModelRulesRector::class;
    }
}
