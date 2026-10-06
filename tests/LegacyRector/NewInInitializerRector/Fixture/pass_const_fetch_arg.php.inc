<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;

const NOW = 'now';

class PassConstFetchArg
{
    private DateTime $dateTime;

    public function __construct(
        ?DateTime $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? new DateTime(NOW);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;

const NOW = 'now';

class PassConstFetchArg
{
    public function __construct(private ?DateTime $dateTime = new DateTime(NOW))
    {
    }
}

?>
