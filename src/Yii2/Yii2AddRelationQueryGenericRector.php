<?php

declare(strict_types=1);

namespace Vix\RectorRules\Yii2;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class Yii2AddRelationQueryGenericRector extends AbstractRector
{
    public function __construct(
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly PhpDocTypeChanger $phpDocTypeChanger,
    ) {

    }

    /**
     * @return RuleDefinition
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Adds the related model type to Yii2 ActiveQuery relation return annotations.',
            [
                new CodeSample(
                    <<<'PHP'
                        /** @return ActiveQuery */
                        public function getAuthor(): ActiveQuery
                        {
                            return $this->hasOne(Author::class, ['id' => 'author_id']);
                        }
                        PHP,
                    <<<'PHP'
                        /** @return ActiveQuery<Author> */
                        public function getAuthor(): ActiveQuery
                        {
                            return $this->hasOne(Author::class, ['id' => 'author_id']);
                        }
                        PHP,
                ),
            ],
        );
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
     *
     * @return ClassMethod|null
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node->returnType instanceof Name || !$this->isName($node->returnType, 'yii\db\ActiveQuery')) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        $returnTagValue = $phpDocInfo?->getReturnTagValue();

        if (!$returnTagValue instanceof ReturnTagValueNode || !$returnTagValue->type instanceof IdentifierTypeNode) {
            return null;
        }

        if ($returnTagValue->type->name !== 'ActiveQuery') {
            return null;
        }

        $relatedModelName = $this->resolveRelatedModelName($node);

        if ($relatedModelName === null) {
            return null;
        }

        $this->phpDocTypeChanger->changeReturnTypeNode(
            $node,
            $phpDocInfo,
            new GenericTypeNode(
                new IdentifierTypeNode('ActiveQuery'),
                [new IdentifierTypeNode($relatedModelName)],
            ),
        );

        return $node;
    }

    private function resolveRelatedModelName(ClassMethod $classMethod): ?string
    {
        $statements = $classMethod->stmts;

        if ($statements === null || count($statements) !== 1 || !$statements[0] instanceof Return_) {
            return null;
        }

        $returnExpression = $statements[0]->expr;

        if (!$returnExpression instanceof MethodCall || !$returnExpression->var instanceof Variable || $returnExpression->var->name !== 'this') {
            return null;
        }

        if (!$this->isNames($returnExpression->name, ['hasOne', 'hasMany'])) {
            return null;
        }

        $firstArgument = $returnExpression->args[0] ?? null;

        if (!$firstArgument instanceof Arg || !$firstArgument->value instanceof ClassConstFetch) {
            return null;
        }

        $classConstFetch = $firstArgument->value;

        if (!$this->isName($classConstFetch->name, 'class') || !$classConstFetch->class instanceof Name) {
            return null;
        }

        return $classConstFetch->class->toString();
    }
}
