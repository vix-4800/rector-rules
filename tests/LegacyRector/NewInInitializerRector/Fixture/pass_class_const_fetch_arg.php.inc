<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\SomeValueObject;

class PassClassConstFetchArg
{
    private DateTime $dateTime;

    public function __construct(
        ?DateTime $dateTime = null
    ) {
        $this->dateTime = $dateTime ?? new DateTime(SomeValueObject::NOW);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Fixture;

use DateTime;
use Vix\RectorRules\Tests\LegacyRector\NewInInitializerRector\Source\SomeValueObject;

class PassClassConstFetchArg
{
    public function __construct(private ?DateTime $dateTime = new DateTime(SomeValueObject::NOW))
    {
    }
}

?>
