<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use function Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\some_view_function;

function anotherFunction()
{
    some_view_function('template');
}
