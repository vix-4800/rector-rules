<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class PropertyFetchInCondition
{
    public function run()
    {
        if ($this->art->netzid > 0 && $artzo_list = $this->artzo_list) {
            if ($artzo_list !== []) {
                foreach ($artzo_list as $art) {
                }
            }
        }
    }
}
?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class PropertyFetchInCondition
{
    public function run()
    {
        if ($this->art->netzid > 0 && ($artzo_list = $this->artzo_list) && $artzo_list !== []) {
            foreach ($artzo_list as $art) {
            }
        }
    }
}
?>
