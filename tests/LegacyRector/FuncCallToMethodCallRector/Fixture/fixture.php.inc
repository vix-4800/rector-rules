<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

final class Fixture
{
    public function run()
    {
        $result = \translate('name');
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

final class Fixture
{
    public function __construct(private \Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\SomeTranslator $someTranslator)
    {
    }
    public function run()
    {
        $result = $this->someTranslator->translateMethod('name');
    }
}

?>
