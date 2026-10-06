<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameUseInstead(SomeApiClient $apiClient)
{
    return $apiClient->getData();
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameUseInstead(SomeApiClient $apiClient)
{
    return $apiClient->fetchData();
}

?>
