<?php

declare(strict_types=1);

namespace Vix\RectorRules\Yii2;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeNameResolver\NodeNameResolver;

final readonly class RelationCallResolver
{
    public function __construct(
        private NodeNameResolver $nodeNameResolver,
    ) {
    }

    /**
     * @return array{relatedClass: Name, isMany: bool}|null
     */
    public function resolve(ClassMethod $classMethod): ?array
    {
        $statements = $classMethod->stmts;

        if ($statements === null || count($statements) !== 1 || !$statements[0] instanceof Return_) {
            return null;
        }

        $returnExpression = $statements[0]->expr;

        if (!$returnExpression instanceof MethodCall || !$returnExpression->var instanceof Variable) {
            return null;
        }

        if ($returnExpression->var->name !== 'this') {
            return null;
        }

        if (!$this->nodeNameResolver->isNames($returnExpression->name, ['hasOne', 'hasMany'])) {
            return null;
        }

        $firstArgument = $returnExpression->args[0] ?? null;

        if (!$firstArgument instanceof Arg || $firstArgument->unpack || !$firstArgument->value instanceof ClassConstFetch) {
            return null;
        }

        $classConstFetch = $firstArgument->value;

        if (!$this->nodeNameResolver->isName($classConstFetch->name, 'class') || !$classConstFetch->class instanceof Name) {
            return null;
        }

        return [
            'relatedClass' => $classConstFetch->class,
            'isMany' => $this->nodeNameResolver->isName($returnExpression->name, 'hasMany'),
        ];
    }
}
