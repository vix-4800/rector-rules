<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

$arrayOfArrays = [['a'], ['a'], ['b']];
$merged = array_unique(array_merge(...$arrayOfArrays));

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\NestedFuncCallsToPipeOperatorRector\Fixture;

$arrayOfArrays = [['a'], ['a'], ['b']];
$merged = array_merge(...$arrayOfArrays)
    |> array_unique(...);

?>
