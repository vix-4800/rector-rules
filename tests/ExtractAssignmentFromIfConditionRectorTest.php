<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vix\RectorRules\ExtractAssignmentFromIfConditionRector;

/**
 * @internal
 */
#[CoversClass(ExtractAssignmentFromIfConditionRector::class)]
final class ExtractAssignmentFromIfConditionRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideExtractsAssignmentsFromIfConditionsCases')]
    #[Test]
    public function extractsAssignmentsFromIfConditions(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideExtractsAssignmentsFromIfConditionsCases(): iterable
    {
        yield 'direct assignment condition' => [
            <<<'PHP'
                <?php

                if ($model = User::findOne($id)) {
                    return $model;
                }
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne($id);
                if ($model) {
                    return $model;
                }
                PHP,
        ];

        yield 'assignment compared to null' => [
            <<<'PHP'
                <?php

                if (($model = User::findOne($id)) !== null) {
                    return $model;
                }
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne($id);
                if ($model !== null) {
                    return $model;
                }
                PHP,
        ];

        yield 'assignment on right side comparison' => [
            <<<'PHP'
                <?php

                if (null !== ($model = User::findOne($id))) {
                    return $model;
                }
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne($id);
                if (null !== $model) {
                    return $model;
                }
                PHP,
        ];

        yield 'boolean not assignment' => [
            <<<'PHP'
                <?php

                if (!($user = $this->findUser())) {
                    return null;
                }
                PHP,
            <<<'PHP'
                <?php

                $user = $this->findUser();
                if (!$user) {
                    return null;
                }
                PHP,
        ];

        yield 'supported function condition' => [
            <<<'PHP'
                <?php

                if (is_array($items = $this->getItems())) {
                    return $items;
                }
                PHP,
            <<<'PHP'
                <?php

                $items = $this->getItems();
                if (is_array($items)) {
                    return $items;
                }
                PHP,
        ];

        yield 'negated supported function condition' => [
            <<<'PHP'
                <?php

                if (!is_null($model = User::findOne($id))) {
                    return $model;
                }
                PHP,
            <<<'PHP'
                <?php

                $model = User::findOne($id);
                if (!is_null($model)) {
                    return $model;
                }
                PHP,
        ];

        yield 'boolean and wraps right extraction' => [
            <<<'PHP'
                <?php

                if ($userId && ($user = User::findIdentity($userId)) !== null) {
                    return $user;
                }
                PHP,
            <<<'PHP'
                <?php

                if ($userId) {
                    $user = User::findIdentity($userId);
                    if ($user !== null) {
                        return $user;
                    }
                }
                PHP,
        ];

        yield 'safe list assignment condition' => [
            <<<'PHP'
                <?php

                if ([$id] = $row['ids']) {
                    return $id;
                }
                PHP,
            <<<'PHP'
                <?php

                [$id] = $row['ids'];
                if ($row['ids']) {
                    return $id;
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsupportedAssignmentsCases')]
    #[Test]
    public function skipsUnsupportedAssignments(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsupportedAssignmentsCases(): iterable
    {
        yield 'boolean and with else' => [
            <<<'PHP'
                <?php

                if ($enabled && ($user = User::findOne($id)) !== null) {
                    return $user;
                } else {
                    return null;
                }
                PHP,
        ];

        yield 'unsupported function' => [
            <<<'PHP'
                <?php

                if (count($items = $this->getItems())) {
                    return $items;
                }
                PHP,
        ];

        yield 'unsafe list assignment expression' => [
            <<<'PHP'
                <?php

                if ([$id] = $this->getIds()) {
                    return $id;
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return ExtractAssignmentFromIfConditionRector::class;
    }
}
