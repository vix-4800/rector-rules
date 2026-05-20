<?php

declare(strict_types=1);

namespace Vix\RectorRules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces repeated comparisons against the same value with in_array() calls
 * Example: $var === 'a' || $var === 'b' || $var === 'c'
 * becomes: in_array($var, ['a', 'b', 'c'], true)
 *
 * Example: $var == 'a' || $var == 'b' || $var == 'c'
 * becomes: in_array($var, ['a', 'b', 'c'])
 *
 * Example: $var !== 'a' && $var !== 'b' && $var !== 'c'
 * becomes: !in_array($var, ['a', 'b', 'c'], true)
 */
final class ReplaceMultipleEqualWithInArrayRector extends AbstractRector implements ConfigurableRectorInterface
{
    public const THRESHOLD = 'threshold';

    private const DEFAULT_THRESHOLD = 3;

    private int $threshold = self::DEFAULT_THRESHOLD;

    /**
     * @return RuleDefinition
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace repeated equality and inequality comparisons with in_array() calls',
            [
                new CodeSample(
                    'if ($var === \'a\' || $var === \'b\' || $var === \'c\') {
                        return true;
                    }',
                    'if (in_array($var, [\'a\', \'b\', \'c\'], true)) {
                        return true;
                    }'
                ),
                new CodeSample(
                    'if ($status == \'active\' || $status == \'pending\' || $status == \'archived\') {
                        // do something
                    }',
                    'if (in_array($status, [\'active\', \'pending\', \'archived\'])) {
                        // do something
                    }'
                ),
                new CodeSample(
                    'if ($direction !== \'top\' && $direction !== \'bottom\' && $direction !== \'left\') {
                        return \'bottom\';
                    }',
                    'if (!in_array($direction, [\'top\', \'bottom\', \'left\'], true)) {
                        return \'bottom\';
                    }'
                ),
                new ConfiguredCodeSample(
                    'if ($status == \'active\' || $status == \'pending\') {
                        return true;
                    }',
                    'if (in_array($status, [\'active\', \'pending\'])) {
                        return true;
                    }',
                    [self::THRESHOLD => 2]
                ),
            ]
        );
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        if ($configuration === []) {
            $this->threshold = self::DEFAULT_THRESHOLD;

            return;
        }

        if (
            !array_key_exists(self::THRESHOLD, $configuration)
            || !is_int($configuration[self::THRESHOLD])
            || $configuration[self::THRESHOLD] < 2
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Configuration "%s" must be an integer greater than or equal to 2.',
                self::THRESHOLD
            ));
        }

        $this->threshold = $configuration[self::THRESHOLD];
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [BooleanAnd::class, BooleanOr::class];
    }

    /**
     * @param BooleanAnd|BooleanOr $node
     */
    public function refactor(Node $node): ?Node
    {
        $comparisons = $this->collectComparisons($node);

        if (!is_array($comparisons)) {
            return null;
        }

        if (count($comparisons) < $this->threshold) {
            return null;
        }

        if (count($comparisons) === 2 && $this->isSimpleNullOrEmptyCheck($comparisons)) {
            return null;
        }

        $isStrict = null;
        $isMixed = false;

        foreach ($comparisons as $comparison) {
            $isCurrentStrict = $comparison instanceof Identical || $comparison instanceof NotIdentical;

            if (!in_array($isStrict, [null, $isCurrentStrict], true)) {
                $isMixed = true;
            }

            $isStrict = $isCurrentStrict;
        }

        if ($isMixed) {
            $isStrict = true;
        }

        $firstSubject = null;
        $values = [];

        foreach ($comparisons as $comparison) {
            $pair = $this->resolveSubjectAndValue($comparison, $firstSubject);

            if ($pair === null) {
                return null;
            }

            if ($firstSubject === null) {
                $firstSubject = $pair['subject'];
            }

            $values[] = $pair['value'];
        }

        if (!$firstSubject instanceof Expr) {
            return null;
        }

        $arrayItems = [];

        foreach ($values as $value) {
            $arrayItems[] = new ArrayItem($value);
        }

        $valuesArray = new Array_($arrayItems);

        $args = [
            new Arg($firstSubject),
            new Arg($valuesArray),
        ];

        if ($isStrict) {
            $args[] = new Arg(new ConstFetch(new Name('true')));
        }

        $funcCall = new FuncCall(
            new Name('in_array'),
            $args
        );

        if ($node instanceof BooleanAnd) {
            return new BooleanNot($funcCall);
        }

        return $funcCall;
    }

    /**
     * @param Equal|Identical|NotEqual|NotIdentical $comparison
     * @param ?Expr                                 $knownSubject
     *
     * @return array{subject: Expr, value: Expr}|null
     */
    private function resolveSubjectAndValue(Node $comparison, ?Expr $knownSubject): ?array
    {
        if ($knownSubject !== null) {
            if ($this->areNodesEqual($comparison->left, $knownSubject)) {
                return [
                    'subject' => $comparison->left,
                    'value' => $comparison->right,
                ];
            }

            if ($this->areNodesEqual($comparison->right, $knownSubject)) {
                return [
                    'subject' => $comparison->right,
                    'value' => $comparison->left,
                ];
            }

            return null;
        }

        if ($this->isLiteralValue($comparison->left) && !$this->isLiteralValue($comparison->right)) {
            return [
                'subject' => $comparison->right,
                'value' => $comparison->left,
            ];
        }

        return [
            'subject' => $comparison->left,
            'value' => $comparison->right,
        ];
    }

    private function isLiteralValue(Node $node): bool
    {
        return $node instanceof ConstFetch
            || $node instanceof Int_
            || $node instanceof String_;
    }

    /**
     * Recursively collects supported comparisons from a boolean chain
     *
     * @param Node $node
     *
     * @return list<Equal|Identical|NotEqual|NotIdentical>|null
     */
    private function collectComparisons(Node $node): ?array
    {
        if ($node instanceof BooleanOr) {
            return $this->collectOrComparisons($node);
        }

        if ($node instanceof BooleanAnd) {
            return $this->collectAndComparisons($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return list<Equal|Identical>|null
     */
    private function collectOrComparisons(Node $node): ?array
    {
        if ($node instanceof Identical || $node instanceof Equal) {
            return [$node];
        }

        if ($node instanceof BooleanOr) {
            $leftComparisons = $this->collectOrComparisons($node->left);
            $rightComparisons = $this->collectOrComparisons($node->right);

            if (!is_array($leftComparisons) || !is_array($rightComparisons)) {
                return null;
            }

            return [...$leftComparisons, ...$rightComparisons];
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return list<NotEqual|NotIdentical>|null
     */
    private function collectAndComparisons(Node $node): ?array
    {
        if ($node instanceof NotIdentical || $node instanceof NotEqual) {
            return [$node];
        }

        if ($node instanceof BooleanAnd) {
            $leftComparisons = $this->collectAndComparisons($node->left);
            $rightComparisons = $this->collectAndComparisons($node->right);

            if (!is_array($leftComparisons) || !is_array($rightComparisons)) {
                return null;
            }

            return [...$leftComparisons, ...$rightComparisons];
        }

        return null;
    }

    /**
     * @param ?Node $node1
     * @param ?Node $node2
     */
    private function areNodesEqual(?Node $node1, ?Node $node2): bool
    {
        if ($node1 === null && $node2 === null) {
            return true;
        }

        if ($node1 === null || $node2 === null) {
            return false;
        }

        return $this->nodeComparator->areNodesEqual($node1, $node2);
    }

    /**
     * Checks if the construct is a simple null/empty string check
     * Such constructs usually appear after refactoring empty() and look better in the original form
     * Examples:
     * - $var === null || $var === ''
     * - $var === '' || $var === null
     * - $var === null || $var === 0
     * - $var === false || $var === null
     *
     * @param list<Equal|Identical|NotEqual|NotIdentical> $comparisons
     */
    private function isSimpleNullOrEmptyCheck(array $comparisons): bool
    {
        if (count($comparisons) !== 2) {
            return false;
        }

        $values = [];

        foreach ($comparisons as $comparison) {
            $pair = $this->resolveSubjectAndValue($comparison, null);

            if ($pair === null || !$pair['subject'] instanceof Variable) {
                return false;
            }

            $values[] = $pair['value'];
        }

        return $this->containsOnlySimpleValues($values);
    }

    /**
     * Checks if the array contains only "simple" values for comparison
     * Simple values: null, empty string, false, true, 0
     *
     * @param list<Node> $values
     */
    private function containsOnlySimpleValues(array $values): bool
    {
        $simpleValueCount = 0;

        foreach ($values as $value) {
            if (!$this->isSimpleValue($value)) {
                continue;
            }

            ++$simpleValueCount;
        }

        return $simpleValueCount === count($values);
    }

    /**
     * Checks if the value is "simple" (null, '', false, true, 0)
     *
     * @param Node $value
     */
    private function isSimpleValue(Node $value): bool
    {
        if ($value instanceof ConstFetch && $value->name->toString() === 'null') {
            return true;
        }

        if ($value instanceof ConstFetch && in_array($value->name->toString(), ['true', 'false'], true)) {
            return true;
        }

        if ($value instanceof String_ && $value->value === '') {
            return true;
        }

        return $value instanceof Int_ && $value->value === 0;
    }
}
