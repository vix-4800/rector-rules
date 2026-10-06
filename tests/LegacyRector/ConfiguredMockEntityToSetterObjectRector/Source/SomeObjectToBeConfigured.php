<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source;

final class SomeObjectToBeConfigured
{
    private string $name = '';

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
