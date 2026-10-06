<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\If_;
use Rector\Rector\AbstractRector;
use Rector\Tests\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector\ChangeOrIfContinueToMultiContinueRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Vix\RectorRules\LegacyRector\NodeManipulator\IfManipulator;

/**
 * @see ChangeOrIfContinueToMultiContinueRectorTest
 */
final class ChangeOrIfContinueToMultiContinueRector extends AbstractRector
{
    /**
     * @readonly
     */
    private IfManipulator $ifManipulator;

    public function __construct(IfManipulator $ifManipulator)
    {
        $this->ifManipulator = $ifManipulator;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change `if a || b` to early return', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function canDrive(Car $newCar)
                        {
                            foreach ($cars as $car) {
                                if ($car->hasWheels() || $car->hasFuel()) {
                                    continue;
                                }

                                $car->setWheel($newCar->wheel);
                                $car->setFuel($newCar->fuel);
                            }
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeClass
                    {
                        public function canDrive(Car $newCar)
                        {
                            foreach ($cars as $car) {
                                if ($car->hasWheels()) {
                                    continue;
                                }
                                if ($car->hasFuel()) {
                                    continue;
                                }

                                $car->setWheel($newCar->wheel);
                                $car->setFuel($newCar->fuel);
                            }
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
        return [If_::class];
    }

    /**
     * @param If_ $node
     *
     * @return list<If_>|null
     */
    public function refactor(Node $node): ?array
    {
        if (!$this->ifManipulator->isIfWithOnly($node, Continue_::class)) {
            return null;
        }

        if (!$node->cond instanceof BooleanOr) {
            return null;
        }

        return $this->processMultiIfContinue($node);
    }

    /**
     * @param If_ $if
     *
     * @return list<If_>|null
     */
    private function processMultiIfContinue(If_ $if): ?array
    {
        $node = clone $if;
        /** @var Continue_ $continue */
        $continue = $if->stmts[0];
        $ifs = $this->createMultipleIfs($if->cond, $continue, []);

        // ensure ifs not removed by other rules
        if ($ifs === []) {
            return null;
        }

        $this->mirrorComments($ifs[0], $node);

        return $ifs;
    }

    /**
     * @param Expr      $expr
     * @param Continue_ $continue
     * @param list<If_> $ifs
     *
     * @return list<If_>
     */
    private function createMultipleIfs(Expr $expr, Continue_ $continue, array $ifs): array
    {
        while ($expr instanceof BooleanOr) {
            $ifs = array_merge($ifs, $this->collectLeftBooleanOrToIfs($expr, $continue, $ifs));
            $ifs[] = new If_($expr->right, ['stmts' => [$continue]]);
            $expr = $expr->right;
        }

        $lastContinueIf = new If_($expr, ['stmts' => [$continue]]);

        // the + is on purpose here, to keep only single continue as last
        return $ifs + [$lastContinueIf];
    }

    /**
     * @param BooleanOr $booleanOr
     * @param Continue_ $continue
     * @param list<If_> $ifs
     *
     * @return list<If_>
     */
    private function collectLeftBooleanOrToIfs(BooleanOr $booleanOr, Continue_ $continue, array $ifs): array
    {
        $left = $booleanOr->left;

        if (!$left instanceof BooleanOr) {
            $if = new If_($left, ['stmts' => [$continue]]);

            return [$if];
        }

        return $this->createMultipleIfs($left, $continue, $ifs);
    }
}
