<?php

declare(strict_types=1);

namespace Vix\RectorRules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\VariadicPlaceholder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class Yii2RedundantActiveRecordSelfLookupRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces redundant lookup of the current Active Record model by id with $this',
            [
                new CodeSample(
                    '$model = self::findOne($this->id);',
                    '$model = $this;'
                ),
                new CodeSample(
                    '$model = self::find()->where([\'id\' => $this->id])->one();',
                    '$model = $this;'
                ),
            ]
        );
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isActiveRecordClass($node)) {
            return null;
        }

        $hasChanged = false;
        $className = $this->getName($node);
        $shortClassName = $node->name?->toString();

        $this->traverseNodesWithCallable($node->stmts, function (Node $node) use ($className, $shortClassName, &$hasChanged): ?Node {
            if ($this->isRedundantFindOneCall($node, $className, $shortClassName)) {
                $hasChanged = true;

                return new Variable('this');
            }

            if ($this->isRedundantFindWhereOneCall($node, $className, $shortClassName)) {
                $hasChanged = true;

                return new Variable('this');
            }

            return null;
        });

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }

    private function isActiveRecordClass(Class_ $class): bool
    {
        if (!$class->extends instanceof Name) {
            return false;
        }

        return in_array($this->getName($class->extends), ['ActiveRecord', 'yii\db\ActiveRecord'], true);
    }

    private function isRedundantFindOneCall(Node $node, ?string $className, ?string $shortClassName): bool
    {
        if (!$node instanceof StaticCall) {
            return false;
        }

        if (!$this->isName($node->name, 'findOne')) {
            return false;
        }

        if (!$this->isCurrentClassStaticCall($node, $className, $shortClassName)) {
            return false;
        }

        if (count($node->args) !== 1) {
            return false;
        }

        $arg = $node->args[0];

        if ($arg instanceof VariadicPlaceholder) {
            return false;
        }

        return $this->isCurrentModelIdExpr($arg->value)
            || $this->isCurrentModelIdArray($arg->value);
    }

    private function isRedundantFindWhereOneCall(Node $node, ?string $className, ?string $shortClassName): bool
    {
        if (!$node instanceof MethodCall || !$this->isName($node->name, 'one')) {
            return false;
        }

        $whereCall = $this->resolveWhereCall($node->var);

        if (!$whereCall instanceof MethodCall) {
            return false;
        }

        $findCall = $whereCall->var;

        if (!$findCall instanceof StaticCall || !$this->isName($findCall->name, 'find')) {
            return false;
        }

        if (!$this->isCurrentClassStaticCall($findCall, $className, $shortClassName)) {
            return false;
        }

        if (count($whereCall->args) !== 1) {
            return false;
        }

        $arg = $whereCall->args[0];

        if ($arg instanceof VariadicPlaceholder) {
            return false;
        }

        return $this->isCurrentModelIdArray($arg->value);
    }

    private function resolveWhereCall(Expr $expr): ?MethodCall
    {
        if ($expr instanceof MethodCall && $this->isName($expr->name, 'where')) {
            return $expr;
        }

        if (!$expr instanceof MethodCall || !$this->isName($expr->name, 'limit')) {
            return null;
        }

        if (!$this->isLimitOneCall($expr)) {
            return null;
        }

        if (!$expr->var instanceof MethodCall || !$this->isName($expr->var->name, 'where')) {
            return null;
        }

        return $expr->var;
    }

    private function isLimitOneCall(MethodCall $methodCall): bool
    {
        if (count($methodCall->args) !== 1) {
            return false;
        }

        $arg = $methodCall->args[0];

        if ($arg instanceof VariadicPlaceholder) {
            return false;
        }

        return $arg->value instanceof Int_ && $arg->value->value === 1;
    }

    private function isCurrentClassStaticCall(StaticCall $staticCall, ?string $className, ?string $shortClassName): bool
    {
        if (!$staticCall->class instanceof Name) {
            return false;
        }

        $calledClassName = $this->getName($staticCall->class);

        return in_array($calledClassName, ['self', 'static', $className, $shortClassName], true);
    }

    private function isCurrentModelIdArray(Expr $expr): bool
    {
        if (!$expr instanceof Array_) {
            return false;
        }

        if (count($expr->items) !== 1) {
            return false;
        }

        $item = $expr->items[0];

        if (!$item->key instanceof String_ || $item->key->value !== 'id') {
            return false;
        }

        return $this->isCurrentModelIdExpr($item->value);
    }

    private function isCurrentModelIdExpr(Expr $expr): bool
    {
        if (!$expr instanceof PropertyFetch) {
            return false;
        }

        if (!$expr->var instanceof Variable || $expr->var->name !== 'this') {
            return false;
        }

        return $expr->name instanceof Identifier && $expr->name->toString() === 'id';
    }
}
