<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source;

if (function_exists('Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Source\some_view_function')) {
    return;
}

function some_view_function()
{

}
