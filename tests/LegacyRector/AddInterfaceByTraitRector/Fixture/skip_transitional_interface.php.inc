<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\AnotherTrait;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\MiddleInterface;

final class SkipTransitionalInterface implements MiddleInterface
{
    use AnotherTrait;
}
