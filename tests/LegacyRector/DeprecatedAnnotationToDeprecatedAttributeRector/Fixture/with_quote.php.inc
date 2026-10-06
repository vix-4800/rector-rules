<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class Fixture
{
    /**
     * Update the specified resource in storage.
     *
     * @deprecated use Internal\Account\TerminalGroupController::update instead
     */
    public function update() {}
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class Fixture
{
    /**
     * Update the specified resource in storage.
     */
    #[\Deprecated(message: 'use Internal\Account\TerminalGroupController::update instead')]
    public function update() {}
}

?>
