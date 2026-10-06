<?php

namespace Vix\RectorRules\Tests\LegacyRector\FluentSettersToStandaloneCallMethodRector\Source;

class SetNameCaller
{
    public function setName(): SurnameCaller
    {
        return new SurnameCaller();
    }
}
