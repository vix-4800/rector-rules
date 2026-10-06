<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SkipMultiStmtsGetter
{
    private string $name;

    public function getName(): string
    {
        $item = 1000;

        return $this->name;
    }
}
