<?php

namespace Vix\RectorRules\Tests\LegacyRector\PropertyHookRector\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

final class SkipMethodWithAttributes
{
    private string $name;

    #[Required]
    public function getName(): string
    {
        return $this->name;
    }

    #[Required]
    public function setName(string $name): void
    {
        $this->name = ucfirst($name);
    }
}
