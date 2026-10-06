<?php

namespace Vix\RectorRules\Tests\LegacyRector\ShortenElseIfRector\Fixture;

class SkipNoElseIf
{
    /**
     * @var bool
     */
    private $cond1;

    /**
     * @var bool
     */
    private $cond2;

    public function run()
    {
        if ($this->cond1) {
            $this->doSomething();
        } else {
            $this->doSomething();
        }
    }

    public function doSomething()
    {

    }
}
