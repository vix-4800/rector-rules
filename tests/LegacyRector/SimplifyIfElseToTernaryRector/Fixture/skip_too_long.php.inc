<?php

namespace Vix\RectorRules\Tests\LegacyRector\SimplifyIfElseToTernaryRector\Fixture;

class SkipTooLong
{
    public function run()
    {
        if ($this->nodeTypeResolver->isStringyType($staticCall->args[1]->value)) {
            $name = $this->nameResolver->isName(
                $staticCall,
                'contains'
            ) ? 'assertStringContainsString' : 'assertStringNotContainsString';
        } else {
            $name = $this->nameResolver->isName($staticCall, 'contains') ? 'assertContains' : 'assertNotContains';
        }
    }
}
