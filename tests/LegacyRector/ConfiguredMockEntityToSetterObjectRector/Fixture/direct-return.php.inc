<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured;

final class DirectReturn extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        return $this->createConfiguredMock(SomeEntityToBeConfigured::class, [
            'getName'=> 'John',
        ]);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured;

final class DirectReturn extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $someEntityToBeConfigured = new \Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured();
        $someEntityToBeConfigured->setName('John');
        return $someEntityToBeConfigured;
    }
}

?>
