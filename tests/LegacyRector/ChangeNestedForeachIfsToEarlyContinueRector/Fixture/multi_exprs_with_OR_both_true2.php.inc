<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedForeachIfsToEarlyContinueRector\Fixture;

class MultiExprsWithOrBothTruthy2
{
    public function run()
    {
        $partPackageList = [ new \StdClass(), 'string' ];
        $payload = [];

        foreach( $partPackageList as $partPackage )
        {
            if(true)
            {
                if (1 === 1 || 2 === 2)
                {
                    if (1 === 1 || 2 === 2)
                    {
                        $payload[] = $partPackage;
                    }
                }
            }
        }

        return json_encode($payload);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeNestedForeachIfsToEarlyContinueRector\Fixture;

class MultiExprsWithOrBothTruthy2
{
    public function run()
    {
        $partPackageList = [ new \StdClass(), 'string' ];
        $payload = [];

        foreach ($partPackageList as $partPackage) {
            if (!true) {
                continue;
            }
            if (!(1 === 1 || 2 === 2)) {
                continue;
            }
            if (!(1 === 1 || 2 === 2)) {
                continue;
            }
            $payload[] = $partPackage;
        }

        return json_encode($payload);
    }
}

?>
