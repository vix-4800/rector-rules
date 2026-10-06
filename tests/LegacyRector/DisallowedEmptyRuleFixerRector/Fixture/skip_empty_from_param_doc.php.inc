<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

class SkipEmptyFromParamDoc
{
    /**
     * @param string $value
     */
    public function getLabel($value)
    {
        if (empty($value)) {

        }
    }
}
