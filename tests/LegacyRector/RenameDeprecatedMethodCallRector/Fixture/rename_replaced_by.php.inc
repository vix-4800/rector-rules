<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameReplacedBy(SomeApiClient $apiClient)
{
    return $apiClient->loadData();
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameReplacedBy(SomeApiClient $apiClient)
{
    return $apiClient->fetchData();
}

?>
