<?php

namespace Vix\RectorRules\Tests\LegacyRector\JsonThrowOnErrorRector\Fixture;

function skipNamedArgumentFlags($str){
    json_decode($str, flags: JSON_THROW_ON_ERROR);
}

