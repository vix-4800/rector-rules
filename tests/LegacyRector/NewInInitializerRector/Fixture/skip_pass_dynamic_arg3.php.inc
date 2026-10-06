<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

class SkipPassDynamicArg3
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $x = 'b';
        $this->logger = $logger ?? new NullLogger(['a' => $x]);
    }
}
