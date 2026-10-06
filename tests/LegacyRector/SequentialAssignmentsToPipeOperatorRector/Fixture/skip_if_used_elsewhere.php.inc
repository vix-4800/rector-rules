<?php

namespace Vix\RectorRules\Tests\LegacyRector\SequentialAssignmentsToPipeOperatorRector\Fixture;

$value = "hello world";
$result1 = function3($value);
$result2 = function2($result1);

$result3 = $result1 + 100;
