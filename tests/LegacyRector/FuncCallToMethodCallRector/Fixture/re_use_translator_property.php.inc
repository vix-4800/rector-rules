<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\PropertyTranslatorProvider;

final class ReUseTranslatorProperty extends PropertyTranslatorProvider
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

use Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\PropertyTranslatorProvider;

final class ReUseTranslatorProperty extends PropertyTranslatorProvider
{
    public function run()
    {
        $result = $this->existingTranslator->translateMethod('name');
    }
}

?>
