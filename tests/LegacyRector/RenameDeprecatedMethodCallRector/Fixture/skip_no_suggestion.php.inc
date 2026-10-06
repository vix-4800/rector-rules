<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function skipNoSuggestion(SomeApiClient $apiClient)
{
    // @deprecated description has no "use newMethod()" hint
    return $apiClient->legacyData();
}
