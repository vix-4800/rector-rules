<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\Rector\AbstractRector;
use Rector\Tests\Php82\Rector\Param\AddSensitiveParameterAttributeRector\AddSensitiveParameterAttributeRectorTest;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use SensitiveParameter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see AddSensitiveParameterAttributeRectorTest
 */
final class AddSensitiveParameterAttributeRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    /**
     * @var string
     */
    public const SENSITIVE_PARAMETERS = 'sensitive_parameters';

    /**
     * @readonly
     */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;

    /**
     * @var list<string>
     */
    private array $sensitiveParameters = [];

    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $parameters = $configuration[self::SENSITIVE_PARAMETERS] ?? [];

        if (!is_array($parameters)) {
            throw new InvalidArgumentException('Sensitive parameter names must be an array.');
        }

        $sensitiveParameters = [];

        foreach ($parameters as $parameter) {
            if (!is_string($parameter)) {
                throw new InvalidArgumentException('Sensitive parameter names must be strings.');
            }

            $sensitiveParameters[] = $parameter;
        }

        $this->sensitiveParameters = $sensitiveParameters;
    }

    public function getNodeTypes(): array
    {
        return [Param::class];
    }

    /**
     * @param Param $node
     */
    public function refactor(Node $node): ?Param
    {
        if (!$this->isNames($node, $this->sensitiveParameters)) {
            return null;
        }

        if ($this->phpAttributeAnalyzer->hasPhpAttribute($node, SensitiveParameter::class)) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified(SensitiveParameter::class))]);

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add SensitiveParameter attribute to method and function configured parameters', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
            class SomeClass
            {
                public function run(string $password)
                {
                }
            }
            CODE_SAMPLE, <<<'CODE_SAMPLE'
            class SomeClass
            {
                public function run(#[\SensitiveParameter] string $password)
                {
                }
            }
            CODE_SAMPLE, [self::SENSITIVE_PARAMETERS => ['password']])]);
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::SENSITIVE_PARAMETER_ATTRIBUTE;
    }
}
