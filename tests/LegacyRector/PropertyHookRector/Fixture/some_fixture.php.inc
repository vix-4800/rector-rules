<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SomeFixture
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = ucfirst($name);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

final class SomeFixture
{
    public string $name {
        get => $this->name;
        set(string $name) {
            $this->name = ucfirst($name);
        }
    }
}

?>
