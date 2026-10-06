<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

/**
 * @deprecated avoid using
 */
trait HandleTrait
{
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

#[\Deprecated(message: 'avoid using')]
trait HandleTrait
{
}

?>
