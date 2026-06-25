<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use Vix\RectorRules\TernaryNullCheckToNullsafeOperatorRector;

/**
 * @internal
 */
#[CoversNothing]
final class TernaryNullCheckToNullsafeOperatorRectorTest extends AbstractRuleTestCase
{
    #[DataProvider('provideReplacesNullCheckTernaryWithNullsafeOperatorCases')]
    public function testReplacesNullCheckTernaryWithNullsafeOperator(string $input, string $expected): void
    {
        $this->doTestCode($input, $expected);
    }

    public static function provideReplacesNullCheckTernaryWithNullsafeOperatorCases(): iterable
    {
        yield 'simple method call – not identical' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? $user->getName() : null;
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->getName();
                PHP,
        ];

        yield 'null on left side of condition' => [
            <<<'PHP'
                <?php

                $name = null !== $user ? $user->getName() : null;
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->getName();
                PHP,
        ];

        yield 'swapped ternary – identical check' => [
            <<<'PHP'
                <?php

                $name = $user === null ? null : $user->getName();
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->getName();
                PHP,
        ];

        yield 'swapped ternary – null on left of identical' => [
            <<<'PHP'
                <?php

                $name = null === $user ? null : $user->getName();
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->getName();
                PHP,
        ];

        yield 'simple property access' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? $user->name : null;
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->name;
                PHP,
        ];

        yield 'method chain two levels' => [
            <<<'PHP'
                <?php

                $city = $user !== null ? $user->getAddress()->getCity() : null;
                PHP,
            <<<'PHP'
                <?php

                $city = $user?->getAddress()->getCity();
                PHP,
        ];

        yield 'method chain three levels' => [
            <<<'PHP'
                <?php

                $code = $order !== null ? $order->getCustomer()->getAddress()->getPostalCode() : null;
                PHP,
            <<<'PHP'
                <?php

                $code = $order?->getCustomer()->getAddress()->getPostalCode();
                PHP,
        ];

        yield 'property then method' => [
            <<<'PHP'
                <?php

                $city = $user !== null ? $user->address->getCity() : null;
                PHP,
            <<<'PHP'
                <?php

                $city = $user?->address->getCity();
                PHP,
        ];

        yield 'method then property' => [
            <<<'PHP'
                <?php

                $city = $user !== null ? $user->getAddress()->city : null;
                PHP,
            <<<'PHP'
                <?php

                $city = $user?->getAddress()->city;
                PHP,
        ];

        yield 'chain already containing nullsafe operator' => [
            <<<'PHP'
                <?php

                $city = $user !== null ? $user->getAddress()?->getCity() : null;
                PHP,
            <<<'PHP'
                <?php

                $city = $user?->getAddress()?->getCity();
                PHP,
        ];

        yield 'method call with arguments' => [
            <<<'PHP'
                <?php

                $result = $repo !== null ? $repo->find($id) : null;
                PHP,
            <<<'PHP'
                <?php

                $result = $repo?->find($id);
                PHP,
        ];

        yield 'method call with multiple arguments' => [
            <<<'PHP'
                <?php

                $result = $repo !== null ? $repo->findBy($field, $value, $limit) : null;
                PHP,
            <<<'PHP'
                <?php

                $result = $repo?->findBy($field, $value, $limit);
                PHP,
        ];

        yield 'inside if condition' => [
            <<<'PHP'
                <?php

                if (($name = $user !== null ? $user->getName() : null) !== null) {
                    echo $name;
                }
                PHP,
            <<<'PHP'
                <?php

                if (($name = $user?->getName()) !== null) {
                    echo $name;
                }
                PHP,
        ];

        yield 'return statement' => [
            <<<'PHP'
                <?php

                function getCity($user) {
                    return $user !== null ? $user->getAddress()->getCity() : null;
                }
                PHP,
            <<<'PHP'
                <?php

                function getCity($user) {
                    return $user?->getAddress()->getCity();
                }
                PHP,
        ];

        yield 'swapped ternary with chain' => [
            <<<'PHP'
                <?php

                $city = $user === null ? null : $user->getAddress()->getCity();
                PHP,
            <<<'PHP'
                <?php

                $city = $user?->getAddress()->getCity();
                PHP,
        ];

        yield 'implicit null check ternary' => [
            <<<'PHP'
                <?php

                $name = $user ? $user->getName() : null;
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->getName();
                PHP,
        ];

        yield 'implicit null check ternary with property chain' => [
            <<<'PHP'
                <?php

                $name = $user ? $user->city->country->name : null;
                PHP,
            <<<'PHP'
                <?php

                $name = $user?->city->country->name;
                PHP,
        ];

        yield 'if condition property chain' => [
            <<<'PHP'
                <?php

                $user = $this->getUser();

                if (
                    $user !== null
                    && $user->city !== null
                    && $user->city->country !== null
                ) {
                    echo $user->city->country->name;
                }
                PHP,
            <<<'PHP'
                <?php

                $user = $this->getUser();

                if (
                    $user?->city?->country !== null
                ) {
                    echo $user->city->country->name;
                }
                PHP,
        ];

        yield 'if condition null on left side' => [
            <<<'PHP'
                <?php

                if (
                    null !== $user
                    && null !== $user->city
                    && null !== $user->city->country
                ) {
                    echo $user->city->country->name;
                }
                PHP,
            <<<'PHP'
                <?php

                if (
                    $user?->city?->country !== null
                ) {
                    echo $user->city->country->name;
                }
                PHP,
        ];

        yield 'if condition method chain' => [
            <<<'PHP'
                <?php

                if (
                    $user !== null
                    && $user->getCity() !== null
                    && $user->getCity()->getCountry() !== null
                ) {
                    echo $user->getCity()->getCountry()->getName();
                }
                PHP,
            <<<'PHP'
                <?php

                if (
                    $user?->getCity()?->getCountry() !== null
                ) {
                    echo $user->getCity()->getCountry()->getName();
                }
                PHP,
        ];

        yield 'if condition property and method chain' => [
            <<<'PHP'
                <?php

                if (
                    $user !== null
                    && $user->city !== null
                    && $user->city->getCountry() !== null
                ) {
                    echo $user->city->getCountry()->getName();
                }
                PHP,
            <<<'PHP'
                <?php

                if (
                    $user?->city?->getCountry() !== null
                ) {
                    echo $user->city->getCountry()->getName();
                }
                PHP,
        ];
    }

    #[DataProvider('provideSkipsUnsupportedPatternsCases')]
    public function testSkipsUnsupportedPatterns(string $input): void
    {
        $this->doTestCode($input);
    }

    public static function provideSkipsUnsupportedPatternsCases(): iterable
    {
        yield 'non-variable subject – method call expression' => [
            <<<'PHP'
                <?php

                $name = $this->getUser() !== null ? $this->getUser()->getName() : null;
                PHP,
        ];

        yield 'non-null else branch' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? $user->getName() : 'anonymous';
                PHP,
        ];

        yield 'non-null if branch in swapped pattern' => [
            <<<'PHP'
                <?php

                $name = $user === null ? 'anonymous' : $user->getName();
                PHP,
        ];

        yield 'different variable in positive branch' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? $other->getName() : null;
                PHP,
        ];

        yield 'positive branch is a function call on subject' => [
            <<<'PHP'
                <?php

                $len = $user !== null ? strlen($user->getName()) : null;
                PHP,
        ];

        yield 'positive branch is a plain variable' => [
            <<<'PHP'
                <?php

                $value = $user !== null ? $user : null;
                PHP,
        ];

        yield 'loose not-equal comparison is not transformed' => [
            <<<'PHP'
                <?php

                $name = $user != null ? $user->getName() : null;
                PHP,
        ];

        yield 'loose equal comparison is not transformed' => [
            <<<'PHP'
                <?php

                $name = $user == null ? null : $user->getName();
                PHP,
        ];

        yield 'ternary without null check' => [
            <<<'PHP'
                <?php

                $name = $user !== '' ? $user->getName() : null;
                PHP,
        ];

        yield 'inverted pattern – positive branch returns null' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? null : $user->getName();
                PHP,
        ];

        yield 'both branches non-null' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? $user->getName() : 'default';
                PHP,
        ];

        yield 'static method call in positive branch' => [
            <<<'PHP'
                <?php

                $name = $user !== null ? UserHelper::format($user) : null;
                PHP,
        ];

        yield 'implicit null check ternary with non-null else branch' => [
            <<<'PHP'
                <?php

                $name = $user ? $user->getName() : 'anonymous';
                PHP,
        ];

        yield 'shorthand ternary is not transformed' => [
            <<<'PHP'
                <?php

                $name = $user ?: null;
                PHP,
        ];

        yield 'if condition with loose null checks' => [
            <<<'PHP'
                <?php

                if (
                    $user != null
                    && $user->city != null
                    && $user->city->country != null
                ) {
                    echo $user->city->country->name;
                }
                PHP,
        ];

        yield 'if condition with extra condition' => [
            <<<'PHP'
                <?php

                if (
                    $user !== null
                    && $user->city !== null
                    && $active
                ) {
                    echo $user->city->name;
                }
                PHP,
        ];

        yield 'if condition with broken chain' => [
            <<<'PHP'
                <?php

                if (
                    $user !== null
                    && $user->city !== null
                    && $profile->country !== null
                ) {
                    echo $profile->country->name;
                }
                PHP,
        ];

        yield 'if condition with non-variable subject' => [
            <<<'PHP'
                <?php

                if (
                    $this->getUser() !== null
                    && $this->getUser()->city !== null
                    && $this->getUser()->city->country !== null
                ) {
                    echo $this->getUser()->city->country->name;
                }
                PHP,
        ];
    }

    protected function getRuleClass(): string
    {
        return TernaryNullCheckToNullsafeOperatorRector::class;
    }
}
