<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

final class PassNonDynamicArg2
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger(['a' => 'b']);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

final class PassNonDynamicArg2
{
    public function __construct(private ?Logger $logger = new NullLogger(['a' => 'b']))
    {
    }
}

?>
