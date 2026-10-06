<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Source\SomeParentContractInterface;

final class SkipParentContract implements SomeParentContractInterface
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }
}
