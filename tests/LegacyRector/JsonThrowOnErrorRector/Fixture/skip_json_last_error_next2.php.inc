<?php

namespace Vix\RectorRules\Tests\LegacyRector\JsonThrowOnErrorRector\Fixture;

function SkipJsonLastErrorNext2()
{
    json_decode($str);
    json_last_error();
    json_last_error_msg();
}

?>
