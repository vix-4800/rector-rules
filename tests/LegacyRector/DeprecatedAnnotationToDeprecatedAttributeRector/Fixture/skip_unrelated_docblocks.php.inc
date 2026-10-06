<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector\Fixture;

/**
 * Documented class.
 * @internal
 */
final class UnrelatedDocblocks
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'name';
    }
}
