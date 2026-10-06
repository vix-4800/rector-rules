<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function skipNotDeprecated(SomeApiClient $apiClient)
{
    return $apiClient->fetchData();
}
