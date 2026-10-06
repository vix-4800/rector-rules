<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\TranslatorProvider;

final class ReUseTranslator extends TranslatorProvider
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

use Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\TranslatorProvider;

final class ReUseTranslator extends TranslatorProvider
{
    public function run()
    {
        $result = $this->getTranslator()->translateMethod('name');
    }
}

?>
