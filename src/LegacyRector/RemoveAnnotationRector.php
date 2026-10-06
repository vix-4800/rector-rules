<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Rector\Tests\DeadCode\Rector\ClassLike\RemoveAnnotationRector\RemoveAnnotationRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see RemoveAnnotationRectorTest
 */
final class RemoveAnnotationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     */
    private PhpDocTagRemover $phpDocTagRemover;

    /**
     * @readonly
     */
    private DocBlockUpdater $docBlockUpdater;

    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;

    /**
     * @var list<string>
     */
    private array $annotationsToRemove = [];

    public function __construct(PhpDocTagRemover $phpDocTagRemover, DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->phpDocTagRemover = $phpDocTagRemover;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove annotation by names', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
            /**
             * @method getName()
             */
            final class SomeClass
            {
            }
            CODE_SAMPLE, <<<'CODE_SAMPLE'
            final class SomeClass
            {
            }
            CODE_SAMPLE, ['method'])]);
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class, FunctionLike::class, Property::class, ClassConst::class];
    }

    /**
     * @param ClassConst|ClassLike|FunctionLike|Property $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->annotationsToRemove === []) {
            throw new InvalidArgumentException('Configure at least one annotation to remove.');
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);

        if (!$phpDocInfo instanceof PhpDocInfo) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->annotationsToRemove as $annotationToRemove) {
            $namedHasChanged = $this->phpDocTagRemover->removeByName($phpDocInfo, $annotationToRemove);

            if ($namedHasChanged) {
                $hasChanged = true;
            }

            if (!is_a($annotationToRemove, PhpDocTagValueNode::class, true)) {
                continue;
            }

            $typedHasChanged = $phpDocInfo->removeByType($annotationToRemove);

            if ($typedHasChanged) {
                $hasChanged = true;
            }
        }

        if ($hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

        return null;
    }

    /**
     * @param list<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $annotationsToRemove = [];

        foreach ($configuration as $annotation) {
            if (!is_string($annotation)) {
                throw new InvalidArgumentException('Annotation names must be strings.');
            }

            $annotationsToRemove[] = $annotation;
        }

        $this->annotationsToRemove = $annotationsToRemove;
    }
}
