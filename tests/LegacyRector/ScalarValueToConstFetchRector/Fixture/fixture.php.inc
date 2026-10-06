<?php

namespace Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Fixture;

$int = 10;
$float = 10.1;
$string = 'ABC';

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Fixture;

$int = \Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_INT;
$float = \Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_FLOAT;
$string = \Vix\RectorRules\Tests\LegacyRector\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_STRING;

?>
