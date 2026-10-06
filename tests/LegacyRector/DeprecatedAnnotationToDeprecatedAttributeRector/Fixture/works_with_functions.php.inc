<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

/**
 * @deprecated some reason.
 */
function works_with_functions()
{
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

#[\Deprecated(message: 'some reason.')]
function works_with_functions()
{
}

?>
