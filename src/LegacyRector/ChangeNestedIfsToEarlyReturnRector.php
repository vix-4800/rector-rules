<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Block;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Finally_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\While_;
use Rector\EarlyReturn\NodeTransformer\ConditionInverter;
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Rector\Tests\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector\ChangeNestedIfsToEarlyReturnRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Vix\RectorRules\LegacyRector\NodeManipulator\IfManipulator;

/**
 * @phpstan-type StmtsAware (Block|
 *     Case_|
 *     Catch_|
 *     ClassMethod|
 *     Do_|
 *     Else_|
 *     ElseIf_|
 *     Finally_|
 *     For_|
 *     Foreach_|
 *     Function_|
 *     If_|
 *     Namespace_|
 *     TryCatch|
 *     While_|
 *     Declare_|
 *     Closure|
 *     FileNode)
 *
 * @see ChangeNestedIfsToEarlyReturnRectorTest
 */
final class ChangeNestedIfsToEarlyReturnRector extends AbstractRector
{
    /**
     * @readonly
     */
    private ConditionInverter $conditionInverter;

    /**
     * @readonly
     */
    private IfManipulator $ifManipulator;

    public function __construct(ConditionInverter $conditionInverter, IfManipulator $ifManipulator)
    {
        $this->conditionInverter = $conditionInverter;
        $this->ifManipulator = $ifManipulator;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change nested ifs to early return', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function run()
                        {
                            if ($value === 5) {
                                if ($value2 === 10) {
                                    return 'yes';
                                }
                            }

                            return 'no';
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function run()
                        {
                            if ($value !== 5) {
                                return 'no';
                            }

                            if ($value2 === 10) {
                                return 'yes';
                            }

                            return 'no';
                        }
                    }
                    CODE_SAMPLE
            )]);
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return NodeGroup::STMTS_AWARE;
    }

    /**
     * @param StmtsAware $node
     *
     * @return StmtsAware
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->stmts === null) {
            return null;
        }

        foreach ($node->stmts as $key => $stmt) {
            if (!$stmt instanceof If_) {
                continue;
            }

            $nextStmt = $node->stmts[$key + 1] ?? null;

            if (!$nextStmt instanceof Return_) {
                return null;
            }

            $nestedIfsWithOnlyReturn = $this->ifManipulator->collectNestedIfsWithOnlyReturn($stmt);

            if ($nestedIfsWithOnlyReturn === []) {
                continue;
            }

            $newStmts = $this->processNestedIfsWithOnlyReturn($nestedIfsWithOnlyReturn, $nextStmt);
            // replace nested ifs with many separate ifs
            array_splice($node->stmts, $key, 1, $newStmts);

            return $node;
        }

        return null;
    }

    /**
     * @param list<If_> $nestedIfsWithOnlyReturn
     * @param Return_   $nextReturn
     *
     * @return list<If_>
     */
    private function processNestedIfsWithOnlyReturn(array $nestedIfsWithOnlyReturn, Return_ $nextReturn): array
    {
        // add nested if openly after this
        $nestedIfsWithOnlyReturnCount = count($nestedIfsWithOnlyReturn);
        $newStmts = [];

        /** @var int $key */
        foreach ($nestedIfsWithOnlyReturn as $key => $nestedIfWithOnlyReturn) {
            // last item → the return node
            if ($nestedIfsWithOnlyReturnCount === $key + 1) {
                $newStmts[] = $nestedIfWithOnlyReturn;
            } else {
                $standaloneIfs = $this->createStandaloneIfsWithReturn($nestedIfWithOnlyReturn, $nextReturn);
                $newStmts = array_merge($newStmts, $standaloneIfs);
            }
        }

        // $newStmts[] = $nextReturn;
        return $newStmts;
    }

    /**
     * @param If_     $onlyReturnIf
     * @param Return_ $return
     *
     * @return list<If_>
     */
    private function createStandaloneIfsWithReturn(If_ $onlyReturnIf, Return_ $return): array
    {
        $invertedCondExpr = $this->conditionInverter->createInvertedCondition($onlyReturnIf->cond);

        // special case
        if ($invertedCondExpr instanceof BooleanNot && $invertedCondExpr->expr instanceof BooleanAnd) {
            $booleanNotPartIf = new If_(new BooleanNot($invertedCondExpr->expr->left));
            $booleanNotPartIf->stmts = [clone $return];
            $secondBooleanNotPartIf = new If_(new BooleanNot($invertedCondExpr->expr->right));
            $secondBooleanNotPartIf->stmts = [clone $return];

            return [$booleanNotPartIf, $secondBooleanNotPartIf];
        }

        $onlyReturnIf->cond = $invertedCondExpr;
        $onlyReturnIf->stmts = [$return];

        return [$onlyReturnIf];
    }
}
