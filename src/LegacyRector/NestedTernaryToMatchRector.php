<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\MatchArm;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class NestedTernaryToMatchRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert nested ternary expressions to match(true) statements', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function getValue($input)
                        {
                            return $input > 100 ? 'more than 100' : ($input > 5 ? 'more than 5' : 'less');
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function getValue($input)
                        {
                            return match (true) {
                                $input > 100 => 'more than 100',
                                $input > 5 => 'more than 5',
                                default => 'less',
                            };
                        }
                    }
                    CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     */
    public function refactor(Node $node): ?Assign
    {
        if (!$node->expr instanceof Ternary) {
            return null;
        }

        $ternary = $node->expr;

        // traverse nested ternaries to collect them all
        /** @var list<array{condition: Expr, result: Expr}> $conditionsAndResults */
        $conditionsAndResults = [];

        $currentTernary = $ternary;

        while (true) {
            if (!$currentTernary->if instanceof Expr) {
                // short ternary, skip
                return null;
            }

            $conditionsAndResults[] = [
                'condition' => $currentTernary->cond,
                'result' => $currentTernary->if,
            ];

            $else = $currentTernary->else;

            if (!$else instanceof Ternary) {
                $defaultExpr = $else;

                break;
            }

            $currentTernary = $else;
        }

        // nothing long enough
        if (count($conditionsAndResults) < 2) {
            return null;
        }

        if (!$this->hasOnlyBooleanConditions($conditionsAndResults)) {
            return null;
        }

        $match = $this->createMatch($conditionsAndResults, $defaultExpr);
        $node->expr = $match;

        return $node;
    }

    /**
     * @param list<array{condition: Expr, result: Expr}> $conditionsAndResults
     */
    private function createMatch(array $conditionsAndResults, Expr $defaultExpr): Match_
    {
        $singleVariableName = $this->matchAlwaysIdenticalVariableName($conditionsAndResults);

        if (is_string($singleVariableName)) {
            $isVariableIdentical = true;
            $match = new Match_(new Variable($singleVariableName));
        } else {
            $isVariableIdentical = false;
            $match = new Match_($this->nodeFactory->createTrue());
        }

        foreach ($conditionsAndResults as $conditionAndResult) {
            $condition = $conditionAndResult['condition'];

            if ($isVariableIdentical && $condition instanceof Identical) {
                $condition = $condition->right;
            }

            $match->arms[] = new MatchArm([
                $condition,
            ], $conditionAndResult['result']);
        }

        $match->arms[] = new MatchArm(conds: null, body: $defaultExpr);

        return $match;
    }

    /**
     * @param list<array{condition: Expr, result: Expr}> $conditionsAndResults
     */
    private function matchAlwaysIdenticalVariableName(array $conditionsAndResults): ?string
    {
        $identicalVariableNames = [];

        foreach ($conditionsAndResults as $conditionAndResult) {
            if (!$conditionAndResult['condition'] instanceof Identical) {
                return null;
            }

            if (!$conditionAndResult['condition']->left instanceof Variable) {
                return null;
            }

            $variableName = $conditionAndResult['condition']->left->name;

            if (!is_string($variableName)) {
                return null;
            }

            $identicalVariableNames[] = $variableName;
        }

        $uniqueIdenticalVariableNames = array_unique($identicalVariableNames);
        $uniqueIdenticalVariableNames = array_values($uniqueIdenticalVariableNames);

        if (count($uniqueIdenticalVariableNames) === 1) {
            return $uniqueIdenticalVariableNames[0];
        }

        return null;
    }

    /**
     * @param list<array{condition: Expr, result: Expr}> $conditionsAndResults
     */
    private function hasOnlyBooleanConditions(array $conditionsAndResults): bool
    {
        foreach ($conditionsAndResults as $conditionAndResult) {
            if (!$this->nodeTypeResolver->getNativeType($conditionAndResult['condition'])->isBoolean()->yes()) {
                return false;
            }
        }

        return true;
    }
}
