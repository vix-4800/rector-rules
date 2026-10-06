<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

/**
 * @deprecated use new constant
 */
const CONSTANT = 'some reason';

/**
 * @deprecated 2.0.0 do not use
 */
const UNUSED = 'ignored';

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ConstAndTraitDeprecatedAttributeRector;

#[\Deprecated(message: 'use new constant')]
const CONSTANT = 'some reason';

#[\Deprecated(message: 'do not use', since: '2.0.0')]
const UNUSED = 'ignored';

?>
