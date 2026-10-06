<?php

namespace Vix\RectorRules\Tests\LegacyRector\SimplifyIfElseToTernaryRector\Fixture;

class MirrorComment
{
    public function run()
    {
        // Admin user ID is 0
        if ( $user_id === 0 ) {
            $access = true;
        } else {
            $access = false;
        }
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\SimplifyIfElseToTernaryRector\Fixture;

class MirrorComment
{
    public function run()
    {
        // Admin user ID is 0
        $access = $user_id === 0 ? true : false;
    }
}

?>
