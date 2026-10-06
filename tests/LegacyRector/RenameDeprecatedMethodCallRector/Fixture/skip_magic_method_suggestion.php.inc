<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\MagicMethodApiClient;

function skipMagicMethodSuggestion(MagicMethodApiClient $apiClient)
{
    // suggested fetchData() exists only as a @method magic method, not a real native one
    return $apiClient->getData();
}
