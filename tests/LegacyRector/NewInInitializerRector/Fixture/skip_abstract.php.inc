<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

abstract class SkipAbstract
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger;
    }
}
