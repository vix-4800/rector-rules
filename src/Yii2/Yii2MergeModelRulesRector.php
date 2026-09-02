<?php

declare(strict_types=1);

namespace Vix\RectorRules\Yii2;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @phpstan-type RuleGroup array{
 *     item: ArrayItem,
 *     attributes: list<string>,
 *     tail: list<ArrayItem>,
 *     count: positive-int
 * }
 */
final class Yii2MergeModelRulesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Merges Yii2 model validation rules with identical validators and options.',
            [
                new CodeSample(
                    <<<'PHP'
                        final class LoginForm extends \yii\base\Model
                        {
                            public function rules(): array
                            {
                                return [
                                    ['login', 'required'],
                                    ['password', 'required'],
                                ];
                            }
                        }
                        PHP,
                    <<<'PHP'
                        final class LoginForm extends \yii\base\Model
                        {
                            public function rules(): array
                            {
                                return [
                                    [['login', 'password'], 'required'],
                                ];
                            }
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
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isModelClass($node)) {
            return null;
        }

        foreach ($node->getMethods() as $method) {
            if (!$this->isName($method, 'rules') || $method->isStatic() || $method->params !== []) {
                continue;
            }

            if ($this->mergeRules($method->stmts)) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @param array<int|string, Node\Stmt>|null $statements
     */
    private function mergeRules(?array $statements): bool
    {
        if ($statements === null || count($statements) !== 1) {
            return false;
        }

        $statements = array_values($statements);
        $statement = $statements[0];

        if (!$statement instanceof Return_ || !$statement->expr instanceof Array_) {
            return false;
        }

        $rules = $statement->expr;

        /** @var list<RuleGroup> $groups */
        $groups = [];

        foreach ($rules->items as $ruleItem) {
            $rule = $this->resolveRule($ruleItem);

            if ($rule === null) {
                return false;
            }

            $groupIndex = $this->findGroup($groups, $rule['tail']);

            if ($groupIndex === null) {
                $groups[] = $this->createGroup(
                    $ruleItem,
                    array_values(array_unique($rule['attributes'])),
                    $rule['tail'],
                );

                continue;
            }

            $group = $groups[$groupIndex];
            $attributes = $group['attributes'];

            foreach ($rule['attributes'] as $attribute) {
                if (!in_array($attribute, $attributes, true)) {
                    $attributes[] = $attribute;
                }
            }

            $groups[$groupIndex] = $this->createGroup(
                $group['item'],
                $attributes,
                $group['tail'],
                $group['count'] + 1,
            );
        }

        $hasChanged = false;
        $mergedItems = [];

        foreach ($groups as $group) {
            if ($group['count'] > 1) {
                /** @var Array_ $rule */
                $rule = $group['item']->value;
                $rule->items = [
                    new ArrayItem($this->createAttributeArray($group['attributes'])),
                    ...$group['tail'],
                ];
                $hasChanged = true;
            }

            $mergedItems[] = $group['item'];
        }

        if (!$hasChanged) {
            return false;
        }

        $rules->items = $mergedItems;

        return true;
    }

    /**
     * @return array{attributes: list<string>, tail: list<ArrayItem>}|null
     */
    private function resolveRule(ArrayItem $ruleItem): ?array
    {
        if ($ruleItem->unpack || !$ruleItem->value instanceof Array_) {
            return null;
        }

        $items = array_values($ruleItem->value->items);

        if (count($items) < 2 || $items[0]->key !== null || $items[0]->unpack) {
            return null;
        }

        $attributes = $this->resolveAttributes($items[0]->value);

        if ($attributes === null) {
            return null;
        }

        $tail = [];

        foreach (array_slice($items, 1) as $item) {
            if ($item->unpack) {
                return null;
            }

            $tail[] = $item;
        }

        return [
            'attributes' => $attributes,
            'tail' => $tail,
        ];
    }

    /**
     * @return list<string>|null
     */
    private function resolveAttributes(Node $node): ?array
    {
        if ($node instanceof String_) {
            return [$node->value];
        }

        if (!$node instanceof Array_) {
            return null;
        }

        $attributes = [];

        foreach ($node->items as $item) {
            if ($item->key !== null || $item->unpack || !$item->value instanceof String_) {
                return null;
            }

            $attributes[] = $item->value->value;
        }

        return $attributes;
    }

    /**
     * @param list<RuleGroup> $groups
     * @param list<ArrayItem> $tail
     */
    private function findGroup(array $groups, array $tail): ?int
    {
        foreach ($groups as $index => $group) {
            if ($this->nodeComparator->areNodesEqual($group['tail'], $tail)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param ArrayItem       $item
     * @param list<string>    $attributes
     * @param list<ArrayItem> $tail
     * @param positive-int    $count
     *
     * @return RuleGroup
     */
    private function createGroup(ArrayItem $item, array $attributes, array $tail, int $count = 1): array
    {
        return [
            'item' => $item,
            'attributes' => $attributes,
            'tail' => $tail,
            'count' => $count,
        ];
    }

    /**
     * @param list<string> $attributes
     */
    private function createAttributeArray(array $attributes): Array_
    {
        $items = [];

        foreach ($attributes as $attribute) {
            $items[] = new ArrayItem(new String_($attribute));
        }

        return new Array_($items);
    }

    private function isModelClass(Class_ $class): bool
    {
        if (!$class->extends instanceof Name) {
            return false;
        }

        if ($this->isObjectType($class, new ObjectType('yii\base\Model'))) {
            return true;
        }

        return mb_strtolower((string) $this->getName($class->extends)) === 'yii\base\model';
    }
}
