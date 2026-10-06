<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\Logger\DefaultLogger;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\Logger\NullLogger;

final class SkipIfNoDefaultNull
{
    private DefaultLogger $logger;

    public function __construct(
        ?DefaultLogger $logger,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }
}
