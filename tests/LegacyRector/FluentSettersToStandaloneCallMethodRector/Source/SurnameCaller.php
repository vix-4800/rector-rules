<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source;

class SurnameCaller
{
    public function setSurname(): self
    {
        return $this;
    }
}
