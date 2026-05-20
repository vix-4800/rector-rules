<?php

declare(strict_types=1);

namespace Vix\RectorRules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\If_;
use Rector\Rector\AbstractRector;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * [RISKY] Replaces ternary null checks with the null-safe operator.
 * This rule is risky because it assumes the subject expression has no side effects;
 * in the original ternary the condition and the method/property access may evaluate
 * the same expression independently, while the null-safe operator evaluates it once.
 * The rule is restricted to simple variables to minimize this risk.
 */
final class TernaryNullCheckToNullsafeOperatorRector extends AbstractRector implements MinPhpVersionInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            '[RISKY] Replaces supported null-check patterns with the null-safe operator',
            [
                new CodeSample(
                    '$name = $user !== null ? $user->getName() : null;',
                    '$name = $user?->getName();'
                ),
                new CodeSample(
                    '$city = $user !== null ? $user->getAddress()->getCity() : null;',
                    '$city = $user?->getAddress()->getCity();'
                ),
                new CodeSample(
                    '$city = $user === null ? null : $user->getAddress()->getCity();',
                    '$city = $user?->getAddress()->getCity();'
                ),
                new CodeSample(
                    '$name = $user ? $user->getName() : null;',
                    '$name = $user?->getName();'
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    if ($user !== null && $user->city !== null && $user->city->country !== null) {
                        echo $user->city->country->name;
                    }
                    CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                    if ($user?->city?->country !== null) {
                        echo $user->city->country->name;
                    }
                    CODE_SAMPLE
                ),
            ]
        );
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::NULLSAFE_OPERATOR;
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Ternary::class, If_::class];
    }

    /**
     * @param Ternary|If_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof If_) {
            return $this->refactorIf($node);
        }

        $extracted = $this->extractNullCheckParts($node);

        if ($extracted === null) {
            return null;
        }

        [$subject, $positiveBranch] = $extracted;

        // Only handle simple variables to avoid changing evaluation count / side effects.
        if (!$subject instanceof Variable) {
            return null;
        }

        return $this->transformChain($positiveBranch, $subject);
    }

    /**
     * Returns [subject, positive_branch] when the ternary is a supported null-check pattern,
     * or null otherwise.
     *
     * Supported patterns:
     *   $var !== null ? $var->… : null
     *   null !== $var ? $var->… : null
     *   $var === null ? null : $var->…
     *   null === $var ? null : $var->…
     *   $var ? $var->… : null
     *
     * @return array{0: Expr, 1: Expr}|null
     */
    private function extractNullCheckParts(Ternary $node): ?array
    {
        // $var !== null ? $var->… : null
        // null !== $var ? $var->… : null
        if ($node->cond instanceof NotIdentical && $node->if !== null) {
            $subject = $this->extractSubjectFromNotNull($node->cond);

            if ($subject !== null && $this->isNullExpr($node->else)) {
                return [$subject, $node->if];
            }
        }

        // $var === null ? null : $var->…
        // null === $var ? null : $var->…
        if ($node->cond instanceof Identical && $node->if !== null) {
            $subject = $this->extractSubjectFromIsNull($node->cond);

            if ($subject !== null && $this->isNullExpr($node->if)) {
                return [$subject, $node->else];
            }
        }

        if ($node->if !== null && $this->isNullExpr($node->else)) {
            return [$node->cond, $node->if];
        }

        return null;
    }

    /**
     * Extracts the non-null operand from `$x !== null` or `null !== $x`.
     */
    private function extractSubjectFromNotNull(NotIdentical $node): ?Expr
    {
        if ($this->isNullExpr($node->right)) {
            return $node->left;
        }

        if ($this->isNullExpr($node->left)) {
            return $node->right;
        }

        return null;
    }

    /**
     * Extracts the non-null operand from `$x === null` or `null === $x`.
     */
    private function extractSubjectFromIsNull(Identical $node): ?Expr
    {
        if ($this->isNullExpr($node->right)) {
            return $node->left;
        }

        if ($this->isNullExpr($node->left)) {
            return $node->right;
        }

        return null;
    }

    private function isNullExpr(Expr $node): bool
    {
        return $node instanceof ConstFetch
            && $node->name->toLowerString() === 'null';
    }

    /**
     * Walks the call/property-fetch chain and replaces the innermost access on
     * $subject with its null-safe counterpart.
     *
     * Returns null when the chain does not start from $subject.
     */
    private function transformChain(Expr $expr, Variable $subject): ?Expr
    {
        if ($expr instanceof MethodCall) {
            if ($this->nodeComparator->areNodesEqual($expr->var, $subject)) {
                return new NullsafeMethodCall($expr->var, $expr->name, $expr->args);
            }

            $transformedVar = $this->transformChain($expr->var, $subject);

            if ($transformedVar === null) {
                return null;
            }

            return new MethodCall($transformedVar, $expr->name, $expr->args);
        }

        if ($expr instanceof PropertyFetch) {
            if ($this->nodeComparator->areNodesEqual($expr->var, $subject)) {
                return new NullsafePropertyFetch($expr->var, $expr->name);
            }

            $transformedVar = $this->transformChain($expr->var, $subject);

            if ($transformedVar === null) {
                return null;
            }

            return new PropertyFetch($transformedVar, $expr->name);
        }

        if ($expr instanceof NullsafeMethodCall || $expr instanceof NullsafePropertyFetch) {
            if ($this->isDirectlyOnSubject($expr, $subject)) {
                // Already null-safe at the root; leave as-is.
                return $expr;
            }

            $transformedVar = $this->transformChain($expr->var, $subject);

            if ($transformedVar === null) {
                return null;
            }

            if ($expr instanceof NullsafeMethodCall) {
                return new NullsafeMethodCall($transformedVar, $expr->name, $expr->args);
            }

            return new NullsafePropertyFetch($transformedVar, $expr->name);
        }

        return null;
    }

    /**
     * Returns true when the expression's receiver is the subject variable itself.
     */
    private function isDirectlyOnSubject(NullsafeMethodCall|NullsafePropertyFetch $expr, Variable $subject): bool
    {
        return $this->nodeComparator->areNodesEqual($expr->var, $subject);
    }

    private function refactorIf(If_ $node): ?If_
    {
        if (!$node->cond instanceof BooleanAnd) {
            return null;
        }

        $checkedExpressions = $this->extractCheckedExpressionsFromBooleanAnd($node->cond);

        if ($checkedExpressions === null || count($checkedExpressions) < 2) {
            return null;
        }

        $subject = $checkedExpressions[0];

        if (!$subject instanceof Variable || !$this->isSupportedNullCheckChain($checkedExpressions)) {
            return null;
        }

        $lastExpression = $checkedExpressions[array_key_last($checkedExpressions)];
        $transformedCondition = $this->transformWholeChain($lastExpression, $subject);

        if ($transformedCondition === null) {
            return null;
        }

        $node->cond = new NotIdentical($transformedCondition, $this->nodeFactory->createNull());

        return $node;
    }

    /**
     * @return list<Expr>|null
     */
    private function extractCheckedExpressionsFromBooleanAnd(BooleanAnd $node): ?array
    {
        $expressions = [];

        foreach ($this->flattenBooleanAnd($node) as $part) {
            if (!$part instanceof NotIdentical) {
                return null;
            }

            $checkedExpression = $this->extractSubjectFromNotNull($part);

            if ($checkedExpression === null) {
                return null;
            }

            $expressions[] = $checkedExpression;
        }

        return $expressions;
    }

    /**
     * @return list<Expr>
     */
    private function flattenBooleanAnd(Expr $expr): array
    {
        if (!$expr instanceof BooleanAnd) {
            return [$expr];
        }

        return [
            ...$this->flattenBooleanAnd($expr->left),
            ...$this->flattenBooleanAnd($expr->right),
        ];
    }

    /**
     * @param list<Expr> $checkedExpressions
     */
    private function isSupportedNullCheckChain(array $checkedExpressions): bool
    {
        for ($i = 1, $count = count($checkedExpressions); $i < $count; ++$i) {
            if (!$this->isDirectChainStep($checkedExpressions[$i - 1], $checkedExpressions[$i])) {
                return false;
            }
        }

        return true;
    }

    private function isDirectChainStep(Expr $previousExpression, Expr $currentExpression): bool
    {
        if (!$currentExpression instanceof MethodCall && !$currentExpression instanceof PropertyFetch) {
            return false;
        }

        return $this->nodeComparator->areNodesEqual($currentExpression->var, $previousExpression);
    }

    private function transformWholeChain(Expr $expr, Variable $subject): ?Expr
    {
        if ($expr instanceof MethodCall) {
            if ($this->nodeComparator->areNodesEqual($expr->var, $subject)) {
                return new NullsafeMethodCall($expr->var, $expr->name, $expr->args);
            }

            $transformedVar = $this->transformWholeChain($expr->var, $subject);

            if ($transformedVar === null) {
                return null;
            }

            return new NullsafeMethodCall($transformedVar, $expr->name, $expr->args);
        }

        if ($expr instanceof PropertyFetch) {
            if ($this->nodeComparator->areNodesEqual($expr->var, $subject)) {
                return new NullsafePropertyFetch($expr->var, $expr->name);
            }

            $transformedVar = $this->transformWholeChain($expr->var, $subject);

            if ($transformedVar === null) {
                return null;
            }

            return new NullsafePropertyFetch($transformedVar, $expr->name);
        }

        return null;
    }
}
