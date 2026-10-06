<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class PropertyWithDefaultValue
{
    public array $items = [];

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function isNotEmpty()
    {
        return ! empty($this->items);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\DisallowedEmptyRuleFixerRector\Fixture;

final class PropertyWithDefaultValue
{
    public array $items = [];

    public function isEmpty()
    {
        return $this->items === [];
    }

    public function isNotEmpty()
    {
        return $this->items !== [];
    }
}

?>
