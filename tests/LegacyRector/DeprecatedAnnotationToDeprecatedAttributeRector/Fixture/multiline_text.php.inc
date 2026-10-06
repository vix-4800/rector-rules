<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class MultilineText
{
    /**
     * @deprecated a reason of the deprecated
     * written in two lines
     */
    public function auth()
    {
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DeprecatedAnnotationToDeprecatedAttributeRector;

final class MultilineText
{
    #[\Deprecated(message: <<<'TXT'
    a reason of the deprecated
     written in two lines
    TXT)]
    public function auth()
    {
    }
}

?>
