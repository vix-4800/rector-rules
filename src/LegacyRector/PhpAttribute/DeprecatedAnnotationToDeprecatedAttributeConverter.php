<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector\PhpAttribute;

use Deprecated;
use LogicException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\PhpDocParser\Ast\PhpDoc\DeprecatedTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory;

final class DeprecatedAnnotationToDeprecatedAttributeConverter
{
    /**
     * @see https://regex101.com/r/qNytVk/1
     *
     * @var string
     */
    private const VERSION_MATCH_REGEX = '/^(?:(\d+\.\d+\.\d+)\s+)?(.*)$/';

    /**
     * @see https://regex101.com/r/SVDPOB/1
     *
     * @var string
     */
    private const START_STAR_SPACED_REGEX = '#^ *\*#ms';

    /**
     * @readonly
     */
    private PhpDocTagRemover $phpDocTagRemover;

    /**
     * @readonly
     */
    private PhpAttributeGroupFactory $phpAttributeGroupFactory;

    /**
     * @readonly
     */
    private DocBlockUpdater $docBlockUpdater;

    /**
     * @readonly
     */
    private PhpDocInfoFactory $phpDocInfoFactory;

    public function __construct(PhpDocTagRemover $phpDocTagRemover, PhpAttributeGroupFactory $phpAttributeGroupFactory, DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->phpDocTagRemover = $phpDocTagRemover;
        $this->phpAttributeGroupFactory = $phpAttributeGroupFactory;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    /**
     * @param ClassConst|ClassMethod|Const_|Function_|Trait_ $node
     */
    public function convert(ClassConst|Function_|ClassMethod|Const_|Trait_ $node): ?Node
    {
        $hasChanged = false;
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);

        if ($phpDocInfo instanceof PhpDocInfo) {
            $deprecatedAttributeGroup = $this->handleDeprecated($phpDocInfo);

            if ($deprecatedAttributeGroup instanceof AttributeGroup) {
                $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
                $node->attrGroups = array_merge($node->attrGroups, [$deprecatedAttributeGroup]);
                $this->removeDeprecatedAnnotations($phpDocInfo);
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    private function handleDeprecated(PhpDocInfo $phpDocInfo): ?AttributeGroup
    {
        $attributeGroup = null;
        $desiredTagValueNodes = $phpDocInfo->getTagsByName('deprecated');

        foreach ($desiredTagValueNodes as $desiredTagValueNode) {
            if (!$desiredTagValueNode->value instanceof DeprecatedTagValueNode) {
                continue;
            }

            $attributeGroup = $this->createAttributeGroup($desiredTagValueNode->value->description);
            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $desiredTagValueNode);

            break;
        }

        return $attributeGroup;
    }

    private function createAttributeGroup(string $annotationValue): AttributeGroup
    {
        if (preg_match(self::VERSION_MATCH_REGEX, $annotationValue, $matches) !== 1) {
            $annotationValue = preg_replace(self::START_STAR_SPACED_REGEX, '', $annotationValue);

            if ($annotationValue === null) {
                throw new LogicException('Invalid deprecation annotation pattern.');
            }

            return new AttributeGroup([new Attribute(new FullyQualified(Deprecated::class), [new Arg(new String_($annotationValue, [AttributeKey::KIND => String_::KIND_NOWDOC, AttributeKey::DOC_LABEL => 'TXT']), false, false, [], new Identifier('message'))])]);
        }

        $since = $matches[1];
        $message = $matches[2];

        return $this->phpAttributeGroupFactory->createFromClassWithItems(Deprecated::class, array_filter(['message' => $message, 'since' => $since]));
    }

    private function removeDeprecatedAnnotations(PhpDocInfo $phpDocInfo): bool
    {
        $hasChanged = false;
        $desiredTagValueNodes = $phpDocInfo->getTagsByName('deprecated');

        foreach ($desiredTagValueNodes as $desiredTagValueNode) {
            if (!$desiredTagValueNode->value instanceof GenericTagValueNode) {
                continue;
            }

            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $desiredTagValueNode);
            $hasChanged = true;
        }

        return $hasChanged;
    }
}
