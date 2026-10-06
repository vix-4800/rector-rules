<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedIfsToEarlyReturnRector\Fixture;

class SkipSingleIf
{
    public function isParameterUsedMethod(Param $param, ClassMethod $classMethod): bool
    {
        $compactFuncCalls = $this->betterNodeFinder->find((array) $classMethod->stmts, function (Node $node): bool {
            if (! $node instanceof FuncCall) {
                return false;
            }

            return $this->nameResolver->isName($node, 'compact');
        });
    }
}
