<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class FluentMethodCallsCollector
{
    /**
     * @readonly
     */
    private NodeTypeResolver $nodeTypeResolver;

    public function __construct(NodeTypeResolver $nodeTypeResolver)
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    /**
     * @return list<MethodCall>
     */
    public function resolve(MethodCall $firstMethodCall): array
    {
        // must be nested method call, so we avoid only single one
        if (!$firstMethodCall->var instanceof MethodCall) {
            return [];
        }

        /** @var list<MethodCall> $methodCalls */
        $methodCalls = [];
        $currentMethodCall = $firstMethodCall;
        $classNameObjectType = null;

        while ($currentMethodCall instanceof MethodCall) {
            if ($currentMethodCall->isFirstClassCallable()) {
                return [];
            }

            // must be exactly one argument
            if (count($currentMethodCall->getArgs()) !== 1) {
                return [];
            }

            $objectType = $this->nodeTypeResolver->getType($currentMethodCall->var);

            $classNames = $objectType->getObjectClassNames();

            if (!$objectType->isObject()->yes() || count($classNames) !== 1) {
                return [];
            }

            if ($classNameObjectType === null) {
                $classNameObjectType = $classNames[0];
            } elseif ($classNameObjectType !== $classNames[0]) {
                return [];
            }

            $methodCalls[] = $currentMethodCall;
            $currentMethodCall = $currentMethodCall->var;
        }

        return $methodCalls;
    }
}
