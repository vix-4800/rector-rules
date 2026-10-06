<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\AdditionalTrait;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;

final class SharedInterface
{
    use SomeTrait;
    use AdditionalTrait;
}

-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\AdditionalTrait;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;

final class SharedInterface implements \Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeInterface
{
    use SomeTrait;
    use AdditionalTrait;
}

