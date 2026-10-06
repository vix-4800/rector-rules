<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;

class SomeClass
{
    use SomeTrait;
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;

class SomeClass implements \Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeInterface
{
    use SomeTrait;
}

?>
