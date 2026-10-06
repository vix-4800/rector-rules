<?php

namespace Vix\RectorRules\Tests\LegacyRector\CombineIfRector\Fixture;

class PropertyFetchInCondition2
{
    public function run()
    {
        if ($artzo_list !== []) {
            if ($this->art->netzid > 0 && $artzo_list = $this->artzo_list) {
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

class PropertyFetchInCondition2
{
    public function run()
    {
        if ($artzo_list !== [] && ($this->art->netzid > 0 && $artzo_list = $this->artzo_list)) {
            foreach ($artzo_list as $art) {
            }
        }
    }
}
?>
