<?php

declare(strict_types=1);

namespace Vix\RectorRules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Turns strict null-coalescing comparisons into isset() or !isset() calls
 * when the expression is safely equivalent.
 *
 * This rule is RISKY because:
 * - For objects implementing ArrayAccess, isset($obj[$key]) calls offsetExists(),
 *   while ($obj[$key] ?? null) calls offsetGet() — these may return different results.
 * - For objects with magic methods, isset($obj->prop) calls __isset(),
 *   while ($obj->prop ?? null) may call __get() — these may behave differently.
 * - For regular arrays and simple variables the transformation is safe.
 */
final class NullCoalescingToIssetRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            '[RISKY] Converts strict null-coalescing comparisons to isset() or !isset() when safely equivalent',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
if (($payload['user'] ?? null) !== null) {
    processUser();
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
if (isset($payload['user'])) {
    processUser();
}
CODE_SAMPLE
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
if (($var ?? null) === null) {
    return;
}
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
if (!isset($var)) {
    return;
}
CODE_SAMPLE
                ),
                new CodeSample(
                    <<<'CODE_SAMPLE'
$result = ($config['key'] ?? null) !== null ? 'yes' : 'no';
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$result = isset($config['key']) ? 'yes' : 'no';
CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Identical::class, NotIdentical::class];
    }

    /**
     * @param Identical|NotIdentical $node
     */
    public function refactor(Node $node): ?Node
    {
        $coalesceExpr = $this->extractNullCoalesceWithNullDefault($node);

        if ($coalesceExpr === null) {
            return null;
        }

        if (!$this->isIssetCompatible($coalesceExpr->left)) {
            return null;
        }

        $isset = new Isset_([$coalesceExpr->left]);

        // ($x ?? null) === null  →  !isset($x)
        if ($node instanceof Identical) {
            return new BooleanNot($isset);
        }

        // ($x ?? null) !== null  →  isset($x)
        return $isset;
    }

    /**
     * Extracts the Coalesce node from a strict null comparison where the coalesce
     * default is also null, e.g.:
     *   ($x ?? null) !== null
     *   null !== ($x ?? null)
     */
    private function extractNullCoalesceWithNullDefault(Identical|NotIdentical $node): ?Coalesce
    {
        if ($node->left instanceof Coalesce && $this->isNullConst($node->right)) {
            if ($this->isNullConst($node->left->right)) {
                return $node->left;
            }
        }

        if ($node->right instanceof Coalesce && $this->isNullConst($node->left)) {
            if ($this->isNullConst($node->right->right)) {
                return $node->right;
            }
        }

        return null;
    }

    private function isNullConst(Expr $expr): bool
    {
        return $expr instanceof ConstFetch && $expr->name->toLowerString() === 'null';
    }

    /**
     * Returns true when the expression is a valid isset() argument.
     * Function calls and method calls are NOT valid isset() arguments.
     */
    private function isIssetCompatible(Expr $expr): bool
    {
        if ($expr instanceof Variable) {
            return true;
        }

        if ($expr instanceof ArrayDimFetch) {
            return true;
        }

        if ($expr instanceof PropertyFetch) {
            return true;
        }

        if ($expr instanceof NullsafePropertyFetch) {
            return true;
        }

        if ($expr instanceof StaticPropertyFetch) {
            return true;
        }

        return false;
    }
}
