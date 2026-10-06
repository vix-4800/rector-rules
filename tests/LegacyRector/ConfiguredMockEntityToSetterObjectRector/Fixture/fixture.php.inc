<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured;

final class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $mockObject = $this->createConfiguredMock(SomeEntityToBeConfigured::class, [
            'getName'=> 'John',
        ]);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured;

final class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $mockObject = new \Vix\RectorRules\Tests\LegacyRector\ConfiguredMockEntityToSetterObjectRector\Source\SomeEntityToBeConfigured();
        $mockObject->setName('John');
    }
}

?>
