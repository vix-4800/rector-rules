<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class MayUninitializedStaticPropertyNoDefaultValue
{
    public static array $items;

    public function isEmpty()
    {
        return empty(self::$items);
    }

    public function isNotEmpty()
    {
        return ! empty(self::$items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class MayUninitializedStaticPropertyNoDefaultValue
{
    public static array $items;

    public function isEmpty()
    {
        return !isset(self::$items) || self::$items === [];
    }

    public function isNotEmpty()
    {
        return isset(self::$items) && self::$items !== [];
    }
}

?>
