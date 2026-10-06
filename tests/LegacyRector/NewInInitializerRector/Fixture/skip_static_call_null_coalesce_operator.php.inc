<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\InstantiableViaNamedConstructor;

final class SkipStaticCallNullCoalesceOperator
{
    public function __construct(private ?InstantiableViaNamedConstructor $foo = null)
    {
        $this->foo ??= InstantiableViaNamedConstructor::make(100);
    }
}
