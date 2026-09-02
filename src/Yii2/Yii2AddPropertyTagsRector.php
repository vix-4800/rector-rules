<?php

declare(strict_types=1);

namespace Vix\RectorRules\Yii2;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PropertyTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\Type\BracketsAwareUnionTypeNode;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\StaticTypeMapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class Yii2AddPropertyTagsRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const string REFINE_PROPERTY_TAG_KINDS = 'refine_property_tag_kinds';

    public const string REMOVE_UNRESOLVED_PROPERTY_TAGS = 'remove_unresolved_property_tags';

    /** @var list<string> */
    private const PROPERTY_TAG_NAMES = ['@property', '@property-read', '@property-write'];

    /** @var list<string> */
    private const KNOWN_MAGIC_ACCESSOR_CLASSES = [
        'yii\\base\\baseobject',
        'yii\\base\\component',
        'yii\\base\\dynamicmodel',
        'yii\\db\\baseactiverecord',
    ];

    private bool $refinePropertyTagKinds = false;

    private bool $removeUnresolvedPropertyTags = false;

    public function __construct(
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly ReflectionProvider $reflectionProvider,
        private readonly RelationCallResolver $relationCallResolver,
        private readonly StaticTypeMapper $staticTypeMapper,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds and corrects Yii2 magic property tags inferred from accessors and ActiveRecord relations.',
            [
                new CodeSample(
                    <<<'PHP'
                        final class Profile extends \yii\base\BaseObject
                        {
                            public function getName(): string
                            {
                                return '';
                            }
                        }
                        PHP,
                    <<<'PHP'
                        /** @property-read string $name */
                        final class Profile extends \yii\base\BaseObject
                        {
                            public function getName(): string
                            {
                                return '';
                            }
                        }
                        PHP,
                ),
                new ConfiguredCodeSample(
                    <<<'PHP'
                        /** @property string $name */
                        final class Profile extends \yii\base\BaseObject
                        {
                            public function getName(): string
                            {
                                return '';
                            }
                        }
                        PHP,
                    <<<'PHP'
                        /** @property-read string $name */
                        final class Profile extends \yii\base\BaseObject
                        {
                            public function getName(): string
                            {
                                return '';
                            }
                        }
                        PHP,
                    [self::REFINE_PROPERTY_TAG_KINDS => true],
                ),
            ],
        );
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $this->refinePropertyTagKinds = $this->resolveBooleanConfiguration(
            $configuration,
            self::REFINE_PROPERTY_TAG_KINDS,
        );
        $this->removeUnresolvedPropertyTags = $this->resolveBooleanConfiguration(
            $configuration,
            self::REMOVE_UNRESOLVED_PROPERTY_TAGS,
        );
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
        if ($node->isAnonymous() || !$this->isBaseObject($node)) {
            return null;
        }

        if ($this->hasCustomMagicAccessors($node)) {
            return null;
        }

        $properties = $this->resolveProperties($node);
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $changed = $this->removeUnresolvedPropertyTags
            ? $this->removeUnresolvedPropertyTags($phpDocInfo, $properties)
            : false;

        foreach ($properties as $propertyName => $property) {
            $changed = $this->updatePropertyTags($phpDocInfo, $propertyName, $property) || $changed;
        }

        if (!$changed) {
            return null;
        }

        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

        return $node;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function resolveBooleanConfiguration(array $configuration, string $option): bool
    {
        if (!array_key_exists($option, $configuration)) {
            return false;
        }

        if (!is_bool($configuration[$option])) {
            throw new InvalidArgumentException(sprintf('Configuration "%s" must be a boolean.', $option));
        }

        return $configuration[$option];
    }

    private function hasCustomMagicAccessors(Class_ $class): bool
    {
        $className = $this->getName($class);

        if ($className === null || !$this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        foreach (['__get', '__set'] as $methodName) {
            if (!$classReflection->hasMethod($methodName)) {
                continue;
            }

            $declaringClassName = strtolower(
                $classReflection->getNativeMethod($methodName)->getDeclaringClass()->getName(),
            );

            if (!in_array($declaringClassName, self::KNOWN_MAGIC_ACCESSOR_CLASSES, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, array{getter: TypeNode|null, setter: TypeNode|null}>
     */
    private function resolveProperties(Class_ $class): array
    {
        $properties = [];
        $isActiveRecord = $this->isActiveRecord($class);

        foreach ($class->getMethods() as $method) {
            if (!$method->isPublic() || $method->isStatic()) {
                continue;
            }

            $methodName = $this->getName($method);

            $getterPropertyName = $this->resolveAccessorPropertyName($methodName, 'get');

            if ($getterPropertyName !== null && $this->hasOnlyOptionalParameters($method->params)) {
                $properties[$getterPropertyName]['getter'] = $this->resolveGetterType($method, $isActiveRecord);
                $properties[$getterPropertyName]['setter'] ??= null;

                continue;
            }

            $setterPropertyName = $this->resolveAccessorPropertyName($methodName, 'set');

            if ($setterPropertyName !== null && $this->hasValidSetterParameters($method->params)) {
                $properties[$setterPropertyName]['setter'] = $this->resolveSetterType($method);
                $properties[$setterPropertyName]['getter'] ??= null;
            }
        }

        return $properties;
    }

    private function isActiveRecord(Class_ $class): bool
    {
        if ($this->isObjectType($class, new ObjectType('yii\\db\\BaseActiveRecord'))) {
            return true;
        }

        if (!$class->extends instanceof Name) {
            return false;
        }

        $parentClassName = strtolower($this->getName($class->extends));

        return $parentClassName === 'yii\\db\\baseactiverecord';
    }

    private function isBaseObject(Class_ $class): bool
    {
        if ($this->isObjectType($class, new ObjectType('yii\\base\\BaseObject'))) {
            return true;
        }

        if (!$class->extends instanceof Name) {
            return false;
        }

        return in_array(
            strtolower($this->getName($class->extends)),
            [
                'yii\\base\\baseobject',
                'yii\\base\\component',
                'yii\\base\\dynamicmodel',
                'yii\\db\\baseactiverecord',
            ],
            true,
        );
    }

    private function resolveAccessorPropertyName(string $methodName, string $prefix): ?string
    {
        $suffix = substr($methodName, strlen($prefix));

        if (!str_starts_with($methodName, $prefix) || $suffix === '' || !ctype_upper($suffix[0])) {
            return null;
        }

        return lcfirst($suffix);
    }

    /**
     * @param array<int|string, Param> $parameters
     */
    private function hasOnlyOptionalParameters(array $parameters): bool
    {
        foreach ($parameters as $parameter) {
            if ($parameter->default === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int|string, Param> $parameters
     */
    private function hasValidSetterParameters(array $parameters): bool
    {
        if ($parameters === []) {
            return false;
        }

        foreach (array_slice($parameters, 1) as $parameter) {
            if ($parameter->default === null) {
                return false;
            }
        }

        return true;
    }

    private function resolveGetterType(ClassMethod $method, bool $isActiveRecord): ?TypeNode
    {
        if ($isActiveRecord) {
            $relation = $this->relationCallResolver->resolve($method);

            if ($relation !== null) {
                $relatedClassType = new IdentifierTypeNode($relation['relatedClass']->toString());

                return $relation['isMany']
                    ? new ArrayTypeNode($relatedClassType)
                    : new BracketsAwareUnionTypeNode([$relatedClassType, new IdentifierTypeNode('null')]);
            }
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($method);
        $returnTag = $phpDocInfo?->getReturnTagValue();

        return $returnTag !== null ? $returnTag->type : $this->resolveNativeType($method->returnType);
    }

    private function resolveSetterType(ClassMethod $method): ?TypeNode
    {
        $parameter = $method->params[0];
        $parameterName = $this->getName($parameter->var);
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($method);
        $paramTag = $parameterName === null ? null : $phpDocInfo?->getParamTagValueByName($parameterName);

        return $paramTag !== null ? $paramTag->type : $this->resolveNativeType($parameter->type);
    }

    private function resolveNativeType(?Node $type): ?TypeNode
    {
        if ($type instanceof NullableType) {
            $innerType = $this->resolveNativeType($type->type);

            return $innerType === null ? null : new NullableTypeNode($innerType);
        }

        if ($type instanceof Identifier || $type instanceof Name) {
            return new IdentifierTypeNode($type->toString());
        }

        if ($type instanceof UnionType) {
            return new UnionTypeNode($this->resolveComplexNativeTypes($type));
        }

        if ($type instanceof IntersectionType) {
            return new IntersectionTypeNode($this->resolveComplexNativeTypes($type));
        }

        return null;
    }

    /**
     * @param UnionType|IntersectionType $type
     *
     * @return list<TypeNode>
     */
    private function resolveComplexNativeTypes(UnionType|IntersectionType $type): array
    {
        $types = [];

        foreach ($type->types as $innerType) {
            $resolvedType = $this->resolveNativeType($innerType);

            if ($resolvedType === null) {
                return [new IdentifierTypeNode('mixed')];
            }

            $types[] = $resolvedType;
        }

        return $types;
    }

    /**
     * @param array{getter: TypeNode|null, setter: TypeNode|null} $property
     */
    private function updatePropertyTags(PhpDocInfo $phpDocInfo, string $propertyName, array $property): bool
    {
        $existingTags = $this->findPropertyTags($phpDocInfo, $propertyName);
        $desiredTags = $this->buildDesiredTags($property);

        if ($desiredTags === []) {
            return false;
        }

        if ($existingTags === []) {
            foreach ($desiredTags as $tagName => $type) {
                $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode(
                    $tagName,
                    new PropertyTagValueNode($type, '$' . $propertyName, ''),
                ));
            }

            return true;
        }

        if ($this->refinePropertyTagKinds) {
            return $this->replacePropertyTags($phpDocInfo, $existingTags, $propertyName, $desiredTags);
        }

        $changed = false;

        foreach ($existingTags as $existingTag) {
            $desiredType = $this->resolveTypeForExistingTag($existingTag, $property);

            if (!$existingTag->value instanceof PropertyTagValueNode) {
                continue;
            }

            if ($desiredType === null || $this->areTypesEqual($existingTag->value->type, $desiredType, $phpDocInfo)) {
                continue;
            }

            $existingTag->value->type = $desiredType;
            $changed = true;
        }

        return $changed;
    }

    /**
     * @param array{getter: TypeNode|null, setter: TypeNode|null} $property
     *
     * @return array<string, TypeNode>
     */
    private function buildDesiredTags(array $property): array
    {
        $getter = $property['getter'];
        $setter = $property['setter'];

        if ($getter !== null && $setter !== null && $this->areTypeNodesEqual($getter, $setter)) {
            return ['@property' => $getter];
        }

        $tags = [];

        if ($getter !== null) {
            $tags['@property-read'] = $getter;
        }

        if ($setter !== null) {
            $tags['@property-write'] = $setter;
        }

        return $tags;
    }

    /**
     * @return list<PhpDocTagNode>
     */
    private function findPropertyTags(PhpDocInfo $phpDocInfo, string $propertyName): array
    {
        $propertyTags = [];

        foreach (self::PROPERTY_TAG_NAMES as $tagName) {
            foreach ($phpDocInfo->getTagsByName($tagName) as $tag) {
                if ($tag->value instanceof PropertyTagValueNode && ltrim($tag->value->propertyName, '$') === $propertyName) {
                    $propertyTags[] = $tag;
                }
            }
        }

        return $propertyTags;
    }

    /**
     * @param array{getter: TypeNode|null, setter: TypeNode|null} $property
     */
    private function resolveTypeForExistingTag(PhpDocTagNode $tag, array $property): ?TypeNode
    {
        if ($tag->name === '@property-write') {
            return $property['setter'] ?? $property['getter'];
        }

        return $property['getter'] ?? $property['setter'];
    }

    /**
     * @param list<PhpDocTagNode> $existingTags
     * @param array<string, TypeNode> $desiredTags
     */
    private function replacePropertyTags(
        PhpDocInfo $phpDocInfo,
        array $existingTags,
        string $propertyName,
        array $desiredTags,
    ): bool {
        $children = array_values($phpDocInfo->getPhpDocNode()->children);
        $firstIndex = null;
        $firstTag = $existingTags[0];

        if (!$firstTag->value instanceof PropertyTagValueNode) {
            return false;
        }

        $description = $firstTag->value->description;

        foreach ($children as $index => $child) {
            if (in_array($child, $existingTags, true)) {
                $firstIndex ??= $index;
                unset($children[$index]);
            }
        }

        $replacementTags = [];

        foreach ($desiredTags as $tagName => $type) {
            $replacementTags[] = new PhpDocTagNode(
                $tagName,
                new PropertyTagValueNode($type, '$' . $propertyName, $description),
            );
        }

        $children = array_values($children);
        array_splice($children, $firstIndex ?? count($children), 0, $replacementTags);
        $phpDocInfo->getPhpDocNode()->children = $children;

        return true;
    }

    /**
     * @param array<string, array{getter: TypeNode|null, setter: TypeNode|null}> $properties
     */
    private function removeUnresolvedPropertyTags(PhpDocInfo $phpDocInfo, array $properties): bool
    {
        $children = $phpDocInfo->getPhpDocNode()->children;
        $changed = false;

        foreach ($children as $index => $child) {
            if (!$child instanceof PhpDocTagNode || !in_array($child->name, self::PROPERTY_TAG_NAMES, true)) {
                continue;
            }

            if (!$child->value instanceof PropertyTagValueNode) {
                continue;
            }

            $propertyName = ltrim($child->value->propertyName, '$');

            if (isset($properties[$propertyName])) {
                continue;
            }

            unset($children[$index]);
            $changed = true;
        }

        if ($changed) {
            $phpDocInfo->getPhpDocNode()->children = $children;
        }

        return $changed;
    }

    private function areTypesEqual(TypeNode $first, TypeNode $second, PhpDocInfo $phpDocInfo): bool
    {
        $firstType = $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType($first, $phpDocInfo->getNode());
        $secondType = $this->staticTypeMapper->mapPHPStanPhpDocTypeNodeToPHPStanType($second, $phpDocInfo->getNode());

        return $firstType->equals($secondType);
    }

    private function areTypeNodesEqual(TypeNode $first, TypeNode $second): bool
    {
        return (string) $first === (string) $second;
    }
}
