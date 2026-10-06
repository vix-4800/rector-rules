<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Pipe;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Block;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Finally_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\VariadicPlaceholder;
use Rector\DeadCode\NodeAnalyzer\ExprUsedInNodeAnalyzer;
use Rector\NodeAnalyzer\ExprAnalyzer;
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Rector\Tests\Php85\Rector\StmtsAwareInterface\SequentialAssignmentsToPipeOperatorRector\SequentialAssignmentsToPipeOperatorRectorTest;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
 * @see https://wiki.php.net/rfc/pipe-operator-v3
 * @see SequentialAssignmentsToPipeOperatorRectorTest
 */
final class SequentialAssignmentsToPipeOperatorRector extends AbstractRector implements MinPhpVersionInterface
{
    /**
     * @readonly
     */
    private ExprAnalyzer $exprAnalyzer;

    private BetterNodeFinder $betterNodeFinder;

    private ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer;

    public function __construct(ExprAnalyzer $exprAnalyzer, BetterNodeFinder $betterNodeFinder, ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer)
    {
        $this->exprAnalyzer = $exprAnalyzer;
        $this->betterNodeFinder = $betterNodeFinder;
        $this->exprUsedInNodeAnalyzer = $exprUsedInNodeAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Transform sequential assignments to pipe operator syntax', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    $value = "hello world";
                    $result1 = function1($value);
                    $result2 = function2($result1);

                    $result = function3($result2);
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    $value = "hello world";

                    $result = $value
                        |> function1(...)
                        |> function2(...)
                        |> function3(...);
                    CODE_SAMPLE
            )]);
    }

    public function getNodeTypes(): array
    {
        return NodeGroup::STMTS_AWARE;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::PIPE_OPERATOER;
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
        $node->stmts = array_values($node->stmts);
        $statements = $node->stmts;
        $totalStatements = count($statements) - 1;

        for ($i = 0; $i < $totalStatements; ++$i) {
            $chain = $this->findAssignmentChain($statements, $i);

            if ($chain && count($chain) >= 2) {
                if ($this->hasUsedIntermediateAssignment($chain)) {
                    continue;
                }

                $statements = $this->processAssignmentChain($statements, $chain, $i);
                $node->stmts = $statements;
                $hasChanged = true;
                $totalStatements = count($statements) - 1;
            }
        }

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }

    /**
     * @param array<int, Stmt> $statements
     * @param int              $startIndex
     *
     * @return list<array{stmt: Expression, assign: Assign, funcCall: FuncCall}>|null
     */
    private function findAssignmentChain(array $statements, int $startIndex): ?array
    {
        $chain = [];
        $currentIndex = $startIndex;
        $totalStatements = count($statements);

        while ($currentIndex < $totalStatements) {
            $stmt = $statements[$currentIndex];

            if (!$stmt instanceof Expression) {
                break;
            }

            $expr = $stmt->expr;

            if (!$expr instanceof Assign) {
                return null;
            }

            if (!$expr->var instanceof Variable || !is_string($expr->var->name)) {
                return null;
            }

            // Check if this is a simple function call with one argument
            if (!$expr->expr instanceof FuncCall) {
                return null;
            }

            $funcCall = $expr->expr;

            if (count($funcCall->args) !== 1) {
                return null;
            }

            $arg = $funcCall->args[0];

            if (!$arg instanceof Arg || $arg->unpack || $arg->name !== null) {
                return null;
            }

            if ($currentIndex === $startIndex) {
                // First in chain - must be a variable or simple value
                if (!$arg->value instanceof Variable && !$this->exprAnalyzer->isDynamicExpr($arg->value)) {
                    return null;
                }

                $chain[] = ['stmt' => $stmt, 'assign' => $expr, 'funcCall' => $funcCall];
            } else {
                // Subsequent in chain - must use previous assignment's variable
                $previousAssign = $chain[count($chain) - 1]['assign'];
                $previousVarName = $this->getName($previousAssign->var);

                if (!$arg->value instanceof Variable || $this->getName($arg->value) !== $previousVarName) {
                    break;
                }

                $chain[] = ['stmt' => $stmt, 'assign' => $expr, 'funcCall' => $funcCall];
            }

            ++$currentIndex;
        }

        return $chain;
    }

    /**
     * @param list<array{stmt: Expression, assign: Assign, funcCall: FuncCall}> $chain
     */
    private function hasUsedIntermediateAssignment(array $chain): bool
    {
        foreach (array_slice($chain, 0, -1) as $index => $item) {
            $variable = $item['assign']->var;

            if (!$variable instanceof Variable) {
                return true;
            }

            $nextArg = $chain[$index + 1]['funcCall']->args[0];

            if (!$nextArg instanceof Arg) {
                return true;
            }

            // Search the entire file, including uses outside a nested statement block.
            $otherUse = $this->betterNodeFinder->findFirst($this->getFile()->getNewStmts(), fn(Node $node): bool => $node !== $variable
                && $node !== $nextArg->value
                && $this->exprUsedInNodeAnalyzer->isUsed($node, $variable));

            if ($otherUse instanceof Node) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<Stmt> $statements
     * @param list<array{stmt: Expression, assign: Assign, funcCall: FuncCall}> $chain
     *
     * @return list<Stmt>
     */
    private function processAssignmentChain(array $statements, array $chain, int $startIndex): array
    {
        $lastAssignment = $chain[count($chain) - 1]['assign'];
        $firstArg = $chain[0]['funcCall']->args[0];

        if (!$firstArg instanceof Arg) {
            return $statements;
        }

        $pipeExpression = $firstArg->value;

        foreach ($chain as $chainItem) {
            $placeholderCall = $this->createPlaceholderCall($chainItem['funcCall']);
            $pipeExpression = new Pipe($pipeExpression, $placeholderCall);
        }

        $assign = new Assign($lastAssignment->var, $pipeExpression);
        array_splice($statements, $startIndex, count($chain), [new Expression($assign)]);

        return $statements;
    }

    private function createPlaceholderCall(FuncCall $funcCall): FuncCall
    {
        return new FuncCall($funcCall->name, [new VariadicPlaceholder()]);
    }
}
