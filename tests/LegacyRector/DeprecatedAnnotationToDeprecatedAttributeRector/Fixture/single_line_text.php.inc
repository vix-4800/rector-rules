<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class SingleLineText
{
    /**
     * @deprecated a reason of the deprecated
     */
    public function auth()
    {
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class SingleLineText
{
    #[\Deprecated(message: 'a reason of the deprecated')]
    public function auth()
    {
    }
}

?>
