<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

final class SkipOnClassConst
{
    /**
     * @deprecated use new constant
     */
    public const CONSTANT = 'some reason';

    /**
     * @deprecated 2.0.0 do not use
     */
    public const UNUSED = 'ignored';
}
