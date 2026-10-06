<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

class SkipAssignCoalesceWithNewInInnerClass
{
    private ?Logger $logger = null;

    public function __construct(
        ?Logger $logger = null
    ) {
        new class {
            public $logger;

            public function init()
            {
                $this->logger = $logger ?? new NullLogger;
            }
        };

        $this->logger = $logger;
    }
}
