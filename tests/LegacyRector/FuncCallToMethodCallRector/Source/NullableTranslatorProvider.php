<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source;

abstract class NullableTranslatorProvider
{
    private $translator;

    public function getTranslator(): ?SomeTranslator
    {
        return $this->translator;
    }
}
