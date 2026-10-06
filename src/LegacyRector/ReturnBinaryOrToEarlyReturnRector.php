<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
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
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Rector\Tests\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector\ReturnBinaryOrToEarlyReturnRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Vix\RectorRules\LegacyRector\Node\AssignAndBinaryMap;
use Vix\RectorRules\LegacyRector\NodeAnalyzer\CallAnalyzer;

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
 *     \PhpParser\Node\Expr\Closure|
 *     FileNode)
 *
 * @see ReturnBinaryOrToEarlyReturnRectorTest
 */
final class ReturnBinaryOrToEarlyReturnRector extends AbstractRector
{
    /**
     * @readonly
     */
    private AssignAndBinaryMap $assignAndBinaryMap;

    /**
     * @readonly
     */
    private CallAnalyzer $callAnalyzer;

    public function __construct(AssignAndBinaryMap $assignAndBinaryMap, CallAnalyzer $callAnalyzer)
    {
        $this->assignAndBinaryMap = $assignAndBinaryMap;
        $this->callAnalyzer = $callAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change single return of `||` to early returns', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function accept()
                        {
                            return $this->something() || $this->somethingElse();
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function accept()
                        {
                            if ($this->something()) {
                                return true;
                            }
                            return (bool) $this->somethingElse();
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
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->stmts === null) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->stmts as $key => $stmt) {
            if (!$stmt instanceof Return_) {
                continue;
            }

            if (!$stmt->expr instanceof BooleanOr) {
                continue;
            }

            $booleanOr = $stmt->expr;
            // the right side becomes the final return, the left operands become early returns
            $left = $booleanOr->left;
            $leftOperands = $left instanceof BooleanOr ? $this->flattenBooleanOr($left) : [$left];
            $ifs = [];

            foreach ($leftOperands as $leftOperand) {
                $ifs[] = new If_($leftOperand, ['stmts' => [new Return_($this->nodeFactory->createTrue())]]);
            }

            if (!$this->callAnalyzer->doesIfHasObjectCall($ifs)) {
                continue;
            }

            $this->mirrorComments($ifs[0], $stmt);
            $lastReturnExpr = $this->assignAndBinaryMap->getTruthyExpr($booleanOr->right);
            $ifsWithLastIf = array_merge($ifs, [new Return_($lastReturnExpr)]);
            array_splice($node->stmts, $key, 1, $ifsWithLastIf);
            $hasChanged = true;
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }

    /**
     * Flatten a left-associative "||" chain into its operands, in source order.
     *
     * @param BooleanOr $booleanOr
     *
     * @return list<Expr>
     */
    private function flattenBooleanOr(BooleanOr $booleanOr): array
    {
        $left = $booleanOr->left;
        $operands = $left instanceof BooleanOr ? $this->flattenBooleanOr($left) : [$left];
        $operands[] = $booleanOr->right;

        return $operands;
    }
}
