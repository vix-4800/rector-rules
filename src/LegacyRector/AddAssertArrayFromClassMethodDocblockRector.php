<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Type\Type;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Rector\Tests\Assert\Rector\ClassMethod\AddAssertArrayFromClassMethodDocblockRector\AddAssertArrayFromClassMethodDocblockRectorTest;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Vix\RectorRules\LegacyRector\Enum\AssertClassName;
use Vix\RectorRules\LegacyRector\NodeAnalyzer\ExistingAssertStaticCallResolver;

/**
 * @experimental Check generic array key/value types in runtime with assert. Generics for impatient people.
 *
 * @see AddAssertArrayFromClassMethodDocblockRectorTest
 */
final class AddAssertArrayFromClassMethodDocblockRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;

    /**
     * @readonly
     */
    private ExistingAssertStaticCallResolver $existingAssertStaticCallResolver;

    private string $assertClass = AssertClassName::WEBMOZART;

    public function __construct(PhpDocInfoFactory $phpDocInfoFactory, ExistingAssertStaticCallResolver $existingAssertStaticCallResolver)
    {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->existingAssertStaticCallResolver = $existingAssertStaticCallResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add key and value assert based on docblock @param type declarations (pick from "webmozart" or "beberlei" asserts)', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
            <?php

            class SomeClass
            {
                /**
                 * @param int[] $items
                 */
                public function run(array $items)
                {
                }
            }

            CODE_SAMPLE, <<<'CODE_SAMPLE'
            <?php

            use Webmozart\Assert\Assert;
            class SomeClass
            {
                /**
                 * @param int[] $items
                 */
                public function run(array $items)
                {
                    Assert::allInteger($items);
                }
            }
            CODE_SAMPLE, [AssertClassName::WEBMOZART])]);
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
        $scope = ScopeFetcher::fetch($node);

        if (!$scope->isInClass()) {
            return null;
        }

        if ($node->stmts === null || $node->isAbstract()) {
            return null;
        }

        $methodPhpDocInfo = $this->phpDocInfoFactory->createFromNode($node);

        if (!$methodPhpDocInfo instanceof PhpDocInfo) {
            return null;
        }

        $paramTagValueNodes = $methodPhpDocInfo->getParamTagValueNodes();

        if ($paramTagValueNodes === []) {
            return null;
        }

        $assertStaticCallStmts = [];

        foreach ($node->getParams() as $param) {
            if (!$param->type instanceof Identifier) {
                continue;
            }

            // handle arrays only
            if (!$this->isName($param->type, 'array')) {
                continue;
            }

            if (!$param->var instanceof Variable) {
                continue;
            }

            $paramName = $param->var->name;

            if (!is_string($paramName)) {
                continue;
            }

            $paramDocType = $methodPhpDocInfo->getParamType($paramName);

            if (!$paramDocType->isArray()->yes()) {
                continue;
            }

            $valueAssertMethod = $this->matchTypeToAssertMethod($paramDocType->getIterableValueType());

            if (is_string($valueAssertMethod)) {
                $assertStaticCallStmts[] = $this->createAssertExpression($param->var, $valueAssertMethod);
            }

            $keyAssertMethod = $this->matchTypeToAssertMethod($paramDocType->getIterableKeyType());

            if (is_string($keyAssertMethod)) {
                $arrayKeys = new FuncCall(new Name('array_keys'), [new Arg($param->var)]);
                $assertStaticCallStmts[] = $this->createAssertExpression($arrayKeys, $keyAssertMethod);
            }
        }

        // filter existing assert to avoid duplication
        if ($assertStaticCallStmts === []) {
            return null;
        }

        $existingAssertCallHashes = $this->existingAssertStaticCallResolver->resolve($node);
        $assertStaticCallStmts = $this->filterOutExistingStaticCall($assertStaticCallStmts, $existingAssertCallHashes);

        if ($assertStaticCallStmts === []) {
            return null;
        }

        $node->stmts = array_merge($assertStaticCallStmts, $node->stmts);

        return $node;
    }

    /**
     * @param array<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        if ($configuration === []) {
            // default
            return;
        }

        if (count($configuration) !== 1 || !isset($configuration[0]) || !in_array($configuration[0], [AssertClassName::BEBERLEI, AssertClassName::WEBMOZART], true)) {
            throw new InvalidArgumentException('Configure exactly one supported assertion class.');
        }

        $this->assertClass = $configuration[0];
    }

    private function createAssertExpression(Expr $expr, string $methodName): Expression
    {
        $assertFullyQualified = new FullyQualified($this->assertClass);
        $staticCall = new StaticCall($assertFullyQualified, $methodName, [new Arg($expr)]);

        return new Expression($staticCall);
    }

    /**
     * @param list<Expression> $assertStaticCallStmts
     * @param list<string>     $existingAssertCallHashes
     *
     * @return list<Expression>
     */
    private function filterOutExistingStaticCall(array $assertStaticCallStmts, array $existingAssertCallHashes): array
    {
        $standard = new Standard();

        return array_values(array_filter($assertStaticCallStmts, static function (Expression $assertStaticCallExpression) use ($standard, $existingAssertCallHashes): bool {
            $currentStaticCallHash = $standard->prettyPrintExpr($assertStaticCallExpression->expr);

            return !in_array($currentStaticCallHash, $existingAssertCallHashes, true);
        }));
    }

    private function matchTypeToAssertMethod(Type $type): ?string
    {
        if ($type->isInteger()->yes()) {
            return 'allInteger';
        }

        if ($type->isString()->yes()) {
            return 'allString';
        }

        if ($type->isFloat()->yes()) {
            return 'allFloat';
        }

        if ($type->isBoolean()->yes()) {
            return 'allBoolean';
        }

        return null;
    }
}
