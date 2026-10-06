<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use function view;

class AnotherView
{
    public function someMethod()
    {
        \view('template', []);
        view('template', []);
    }
}

?>
-----
<?php

namespace Vix\RectorRules\Tests\LegacyRector\FuncCallToMethodCallRector\Fixture;

use function view;

class AnotherView
{
    public function __construct(private \Namespaced\SomeRenderer $someRenderer)
    {
    }
    public function someMethod()
    {
        $this->someRenderer->render('template', []);
        $this->someRenderer->render('template', []);
    }
}

?>
