<?php

namespace Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Fixture;

use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeInterface;
use Vix\RectorRules\Tests\LegacyRector\AddInterfaceByTraitRector\Source\SomeTrait;

class SkipExisting implements SomeInterface
{
    use SomeTrait;
}
