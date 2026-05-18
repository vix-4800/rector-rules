<?php

declare(strict_types=1);

namespace Vix\RectorRules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\VariadicPlaceholder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * This Rector rule simplifies redundant array usage in findOne().
 * Specifically, it transforms:
 *     Model::findOne(['id' => $id])
 * into:
 *     Model::findOne($id)
 *
 * This only applies when the array contains a single key 'id'.
 */
final class Yii2FindOneIdShortcutRector extends AbstractRector
{
    /**
     * Provides documentation and example code for the rule.
     *
     * @return RuleDefinition
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces Model::findOne([\'id\' => $id]) with Model::findOne($id)',
            [],
        );
    }

    /**
     * Specifies the node types this Rector rule should process.
     *
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * Performs the transformation for matching StaticCall nodes.
     *
     * @param StaticCall $node
     *
     * @return StaticCall|null
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node->name instanceof Identifier || $node->name->toString() !== 'findOne') {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $firstArg = $node->args[0];

        if ($firstArg instanceof VariadicPlaceholder || !$firstArg->value instanceof Array_) {
            return null;
        }

        $array = $firstArg->value;

        if (count($array->items) !== 1) {
            return null;
        }

        $item = $array->items[0];

        if (!$item->key instanceof String_ || $item->key->value !== 'id') {
            return null;
        }

        return new StaticCall(
            $node->class,
            new Identifier('findOne'),
            [new Arg($item->value)],
        );
    }
}
