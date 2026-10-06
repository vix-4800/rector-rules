<?php

namespace Vix\RectorRules\Tests\LegacyRector\JsonThrowOnErrorRector\Fixture;

function SkipJsonLastErrorNext($str)
{
    json_decode($str);
    return json_last_error() === JSON_ERROR_NONE;
}

?>
