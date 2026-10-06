<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ClassReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Rector\Tests\Transform\Rector\Class_\AddInterfaceByTraitRector\AddInterfaceByTraitRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @api used in rector-doctrine
 *
 * @see AddInterfaceByTraitRectorTest
 */
final class AddInterfaceByTraitRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $interfaceByTrait = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add interface by used trait', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
            class SomeClass
            {
                use SomeTrait;
            }
            CODE_SAMPLE, <<<'CODE_SAMPLE'
            class SomeClass implements SomeInterface
            {
                use SomeTrait;
            }
            CODE_SAMPLE, ['SomeTrait' => 'SomeInterface'])]);
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $scope = ScopeFetcher::fetch($node);
        $classReflection = $scope->getClassReflection();

        if (!$classReflection instanceof ClassReflection) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->interfaceByTrait as $traitName => $interfaceName) {
            if (!$classReflection->hasTraitUse($traitName)) {
                continue;
            }

            if ($classReflection->implementsInterface($interfaceName)) {
                continue;
            }

            foreach ($node->implements as $implementedInterface) {
                if ($this->isName($implementedInterface, $interfaceName)) {
                    continue 2;
                }
            }

            $node->implements[] = new FullyQualified($interfaceName);
            $hasChanged = true;
        }

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }

    /**
     * @param array<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $interfaceByTrait = [];

        foreach ($configuration as $trait => $interface) {
            if (!is_string($trait) || !is_string($interface)) {
                throw new InvalidArgumentException('Trait and interface names must be strings.');
            }

            $interfaceByTrait[$trait] = $interface;
        }

        $this->interfaceByTrait = $interfaceByTrait;
    }
}
