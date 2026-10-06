<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Rector\AbstractRector;
use Rector\Tests\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForDimFetchArrayFromAssignsRector\AddReturnDocblockForDimFetchArrayFromAssignsRectorTest;
use Rector\TypeDeclarationDocblocks\NodeFinder\ReturnNodeFinder;
use Rector\TypeDeclarationDocblocks\TagNodeAnalyzer\UsefulArrayTagNodeAnalyzer;
use Rector\TypeDeclarationDocblocks\TypeResolver\ConstantArrayTypeGeneralizer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AddReturnDocblockForDimFetchArrayFromAssignsRectorTest
 */
final class AddReturnDocblockForDimFetchArrayFromAssignsRector extends AbstractRector
{
    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;

    /**
     * @readonly
     */
    private UsefulArrayTagNodeAnalyzer $usefulArrayTagNodeAnalyzer;

    /**
     * @readonly
     */
    private ReturnNodeFinder $returnNodeFinder;

    /**
     * @readonly
     */
    private ConstantArrayTypeGeneralizer $constantArrayTypeGeneralizer;

    /**
     * @readonly
     */
    private PhpDocTypeChanger $phpDocTypeChanger;

    public function __construct(PhpDocInfoFactory $phpDocInfoFactory, UsefulArrayTagNodeAnalyzer $usefulArrayTagNodeAnalyzer, ReturnNodeFinder $returnNodeFinder, ConstantArrayTypeGeneralizer $constantArrayTypeGeneralizer, PhpDocTypeChanger $phpDocTypeChanger)
    {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->usefulArrayTagNodeAnalyzer = $usefulArrayTagNodeAnalyzer;
        $this->returnNodeFinder = $returnNodeFinder;
        $this->constantArrayTypeGeneralizer = $constantArrayTypeGeneralizer;
        $this->phpDocTypeChanger = $phpDocTypeChanger;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add @return docblock for methods returning array from dim fetch of assigned arrays', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    final class SomeClass
                    {
                        public function toArray(): array
                        {
                            $items = [];

                            if (mt_rand(0, 1)) {
                                $items['key'] = 'value';
                            }

                            if (mt_rand(0, 1)) {
                                $items['another_key'] = 'another_value';
                            }

                            return $items;
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    final class SomeClass
                    {
                        /**
                         * @return array<string, string>
                         */
                        public function toArray()
                        {
                            $items = [];

                            if (mt_rand(0, 1)) {
                                $items['key'] = 'value';
                            }

                            if (mt_rand(0, 1)) {
                                $items['another_key'] = 'another_value';
                            }

                            return $items;
                        }
                    }
                    CODE_SAMPLE
            )]);
    }

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?ClassMethod
    {
        if ($node->stmts === null) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        if ($this->usefulArrayTagNodeAnalyzer->isUsefulArrayTag($phpDocInfo->getReturnTagValue())) {
            return null;
        }

        $soleReturn = $this->returnNodeFinder->findOnlyReturnWithExpr($node);

        if (!$soleReturn instanceof Return_) {
            return null;
        }

        // only variable
        if (!$soleReturn->expr instanceof Variable) {
            return null;
        }

        $returnedExprType = $this->getType($soleReturn->expr);

        if (!$returnedExprType->isConstantArray()->yes()) {
            return null;
        }

        // find stmts with $item = [];
        $returnedVariableName = $this->getName($soleReturn->expr);

        if (!is_string($returnedVariableName)) {
            return null;
        }

        if (!$this->isVariableInstantiated($node, $returnedVariableName)) {
            return null;
        }

        if ($returnedExprType->getReferencedClasses() !== []) {
            // better handled by shared-interface/class rule, to avoid turning objects to mixed
            return null;
        }

        $constantArrays = $returnedExprType->getConstantArrays();
        $genericTypeNodes = [];

        foreach ($constantArrays as $constantArray) {
            // Other array branches already include the empty array.
            if (count($constantArrays) > 1 && $constantArray->getKeyTypes() === []) {
                continue;
            }

            $genericTypeNode = $this->constantArrayTypeGeneralizer->generalize($constantArray);
            $genericTypeNodes[(string) $genericTypeNode] = $genericTypeNode;
        }

        $genericTypeNodes = array_values($genericTypeNodes);

        if ($genericTypeNodes === []) {
            return null;
        }

        $returnTypeNode = count($genericTypeNodes) === 1
            ? $genericTypeNodes[0]
            : new UnionTypeNode($genericTypeNodes);
        $this->phpDocTypeChanger->changeReturnTypeNode($node, $phpDocInfo, $returnTypeNode);

        return $node;
    }

    private function isVariableInstantiated(ClassMethod $classMethod, string $returnedVariableName): bool
    {
        foreach ((array) $classMethod->stmts as $stmt) {
            if (!$stmt instanceof Expression) {
                continue;
            }

            if (!$stmt->expr instanceof Assign) {
                continue;
            }

            $assign = $stmt->expr;

            if (!$assign->var instanceof Variable) {
                continue;
            }

            if (!$this->isName($assign->var, $returnedVariableName)) {
                continue;
            }

            // must be array assignment
            if (!$assign->expr instanceof Array_) {
                continue;
            }

            return true;
        }

        return false;
    }
}
