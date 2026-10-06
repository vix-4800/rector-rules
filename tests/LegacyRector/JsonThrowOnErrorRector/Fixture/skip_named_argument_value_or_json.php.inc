<?php

namespace Vix\RectorRules\Tests\LegacyRector\JsonThrowOnErrorRector\Fixture;

function skipNamedArgumentValueOrJson($str, array $data){
    json_decode(json: $str);
    json_encode(value: $data);
}
