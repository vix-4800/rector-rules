<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\NullCoalescingToIssetRector;

final class NullCoalescingToIssetRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideChangedCases')]
    public function testConvertsNullCoalescingComparisonToIsset(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    #[DataProvider('provideUnchangedCases')]
    public function testSkipsUnsupportedExpressions(string $input): void
    {
        $this->doTestCode($input);
    }

    protected function getRuleClass(): string
    {
        return NullCoalescingToIssetRector::class;
    }

    public static function provideChangedCases(): iterable
    {
        yield 'array key not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if (($payload['user'] ?? null) !== null) {
                processUser();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($payload['user'])) {
                processUser();
            }
            PHP,
        ];

        yield 'array key identical to null becomes not isset' => [
            <<<'PHP'
            <?php

            if (($payload['user'] ?? null) === null) {
                return;
            }
            PHP,
            <<<'PHP'
            <?php

            if (!isset($payload['user'])) {
                return;
            }
            PHP,
        ];

        yield 'null on left side of not-identical becomes isset' => [
            <<<'PHP'
            <?php

            if (null !== ($payload['user'] ?? null)) {
                processUser();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($payload['user'])) {
                processUser();
            }
            PHP,
        ];

        yield 'null on left side of identical becomes not isset' => [
            <<<'PHP'
            <?php

            if (null === ($payload['user'] ?? null)) {
                return;
            }
            PHP,
            <<<'PHP'
            <?php

            if (!isset($payload['user'])) {
                return;
            }
            PHP,
        ];

        yield 'simple variable not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if (($var ?? null) !== null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($var)) {
                doSomething();
            }
            PHP,
        ];

        yield 'simple variable identical to null becomes not isset' => [
            <<<'PHP'
            <?php

            if (($var ?? null) === null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (!isset($var)) {
                doSomething();
            }
            PHP,
        ];

        yield 'property fetch not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if (($obj->prop ?? null) !== null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($obj->prop)) {
                doSomething();
            }
            PHP,
        ];

        yield 'property fetch identical to null becomes not isset' => [
            <<<'PHP'
            <?php

            if (($obj->prop ?? null) === null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (!isset($obj->prop)) {
                doSomething();
            }
            PHP,
        ];

        yield 'nullsafe property fetch not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if (($obj?->prop ?? null) !== null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($obj?->prop)) {
                doSomething();
            }
            PHP,
        ];

        yield 'static property fetch not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if ((Foo::$bar ?? null) !== null) {
                doSomething();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset(Foo::$bar)) {
                doSomething();
            }
            PHP,
        ];

        yield 'nested array access not-identical to null becomes isset' => [
            <<<'PHP'
            <?php

            if (($data['key']['nested'] ?? null) !== null) {
                process();
            }
            PHP,
            <<<'PHP'
            <?php

            if (isset($data['key']['nested'])) {
                process();
            }
            PHP,
        ];

        yield 'used in ternary expression' => [
            <<<'PHP'
            <?php

            $result = ($config['key'] ?? null) !== null ? 'yes' : 'no';
            PHP,
            <<<'PHP'
            <?php

            $result = isset($config['key']) ? 'yes' : 'no';
            PHP,
        ];

        yield 'used in return statement' => [
            <<<'PHP'
            <?php

            function hasUser(array $data): bool
            {
                return ($data['user'] ?? null) !== null;
            }
            PHP,
            <<<'PHP'
            <?php

            function hasUser(array $data): bool
            {
                return isset($data['user']);
            }
            PHP,
        ];

        yield 'used in assignment' => [
            <<<'PHP'
            <?php

            $hasKey = ($arr['key'] ?? null) !== null;
            PHP,
            <<<'PHP'
            <?php

            $hasKey = isset($arr['key']);
            PHP,
        ];
    }

    public static function provideUnchangedCases(): iterable
    {
        yield 'non-null coalesce default is not transformed' => [
            <<<'PHP'
            <?php

            if (($arr['key'] ?? false) !== null) {
                doSomething();
            }
            PHP,
        ];

        yield 'non-null coalesce default on other side is not transformed' => [
            <<<'PHP'
            <?php

            if (($arr['key'] ?? 0) !== null) {
                doSomething();
            }
            PHP,
        ];

        yield 'comparing to non-null value is not transformed' => [
            <<<'PHP'
            <?php

            if (($arr['key'] ?? null) !== false) {
                doSomething();
            }
            PHP,
        ];

        yield 'function call result is not valid isset argument' => [
            <<<'PHP'
            <?php

            if ((getUser() ?? null) !== null) {
                doSomething();
            }
            PHP,
        ];

        yield 'method call result is not valid isset argument' => [
            <<<'PHP'
            <?php

            if (($obj->getUser() ?? null) !== null) {
                doSomething();
            }
            PHP,
        ];

        yield 'loose not-equal is not transformed (risky: 0 == null in PHP)' => [
            <<<'PHP'
            <?php

            if (($arr['key'] ?? null) != null) {
                doSomething();
            }
            PHP,
        ];

        yield 'loose equal is not transformed (risky: 0 == null in PHP)' => [
            <<<'PHP'
            <?php

            if (($arr['key'] ?? null) == null) {
                doSomething();
            }
            PHP,
        ];

        yield 'coalesce without null comparison is not transformed' => [
            <<<'PHP'
            <?php

            $value = $arr['key'] ?? null;
            PHP,
        ];

        yield 'plain strict null comparison without coalesce is not transformed' => [
            <<<'PHP'
            <?php

            if ($var !== null) {
                doSomething();
            }
            PHP,
        ];
    }
}
