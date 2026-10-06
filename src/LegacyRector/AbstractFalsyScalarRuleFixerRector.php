<?php

declare(strict_types=1);

namespace Vix\RectorRules\LegacyRector;

use InvalidArgumentException;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;

/**
 * @internal
 */
abstract class AbstractFalsyScalarRuleFixerRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @api
     *
     * @var string
     */
    public const TREAT_AS_NON_EMPTY = 'treat_as_non_empty';

    protected bool $treatAsNonEmpty = false;

    /**
     * @param array<string, mixed> $configuration
     */
    final public function configure(array $configuration): void
    {
        $treatAsNonEmpty = $configuration[self::TREAT_AS_NON_EMPTY] ?? (bool) current($configuration);

        if (!is_bool($treatAsNonEmpty)) {
            throw new InvalidArgumentException('The treat_as_non_empty option must be a boolean.');
        }

        $this->treatAsNonEmpty = $treatAsNonEmpty;
    }
}
