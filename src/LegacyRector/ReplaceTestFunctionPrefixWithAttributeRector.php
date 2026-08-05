<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\Attributes\Test;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory;
use Rector\PHPUnit\Enum\PHPUnitAttribute;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ReplaceTestFunctionPrefixWithAttributeRector extends AbstractRector
{
    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        private readonly PhpAttributeGroupFactory $phpAttributeGroupFactory,
        private readonly PhpAttributeAnalyzer $phpAttributeAnalyzer,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace test-prefixed function with #[Test] attribute', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    class SomeTest extends \PHPUnit\Framework\TestCase
                    {
                        public function testOnePlusOneShouldBeTwo()
                        {
                            $this->assertSame(2, 1+1);
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    class SomeTest extends \PHPUnit\Framework\TestCase
                    {
                        #[Test]
                        public function onePlusOneShouldBeTwo()
                        {
                            $this->assertSame(2, 1+1);
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
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        if (!$node->isPublic()) {
            return null;
        }

        $methodName = $node->name->toString();

        if (!str_starts_with($methodName, 'test')) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttributes($node, [PHPUnitAttribute::TEST])) {
            return null;
        }

        if ($methodName !== 'test' && $methodName !== 'test_') {
            if (str_starts_with($methodName, 'test_')) {
                $renamedMethodName = lcfirst(substr($methodName, 5));
            } else {
                $renamedMethodName = lcfirst(substr($methodName, 4));
            }

            if ($renamedMethodName === '') {
                return null;
            }

            $node->name->name = $renamedMethodName;
        }

        $coversAttributeGroup = $this->createAttributeGroup();
        $node->attrGroups = array_merge($node->attrGroups, [$coversAttributeGroup]);

        return $node;
    }

    private function createAttributeGroup(): AttributeGroup
    {
        return $this->phpAttributeGroupFactory->createFromClassWithItems(Test::class, []);
    }
}
