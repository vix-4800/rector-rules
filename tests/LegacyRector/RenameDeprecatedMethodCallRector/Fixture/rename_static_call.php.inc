<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameStaticCall()
{
    return SomeApiClient::makeOld();
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\RenameDeprecatedMethodCallRector\Source\SomeApiClient;

function renameStaticCall()
{
    return SomeApiClient::make();
}

?>
