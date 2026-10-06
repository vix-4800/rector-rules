<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\InstantiableViaNamedConstructor;

final class SkipStaticCallRightCoalesce
{
    private InstantiableViaNamedConstructor $foo;

    public function __construct(?InstantiableViaNamedConstructor $foo = null)
    {
        $this->foo = $foo ?? InstantiableViaNamedConstructor::make(100);
    }

}
